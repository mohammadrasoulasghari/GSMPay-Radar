<?php

namespace App\Models;

use App\ValueObjects\AiAnalysis;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_id',
        'repository',
        'pr_number',
        'pr_link',
        'title',
        'business_value_score',
        'solid_compliance_score',
        'tone_score',
        'health_status',
        'risk_level',
        'change_type',
        'raw_analysis',
    ];

    protected $casts = [
        'raw_analysis' => 'array',
        'business_value_score' => 'integer',
        'solid_compliance_score' => 'integer',
        'tone_score' => 'decimal:2',
    ];

    /** @var AiAnalysis|null|false Cached AI analysis (false = not yet loaded) */
    private AiAnalysis|null|false $_aiAnalysisCache = false;

    /**
     * Get the developer that owns this report.
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    /**
     * Get the AI analysis as a Value Object.
     * Returns null if data is empty, invalid, or parsing fails.
     * Result is cached for the lifetime of this model instance.
     */
    public function getAiAnalysis(): ?AiAnalysis
    {
        // Return cached result if already loaded
        if ($this->_aiAnalysisCache !== false) {
            return $this->_aiAnalysisCache;
        }

        if (empty($this->raw_analysis) || !is_array($this->raw_analysis)) {
            $this->_aiAnalysisCache = null;
            return null;
        }

        // AiAnalysis::fromArray returns null on empty/invalid data
        $this->_aiAnalysisCache = AiAnalysis::fromArray($this->raw_analysis);
        return $this->_aiAnalysisCache;
    }

    /**
     * Clear the AI analysis cache (useful after updating raw_analysis).
     */
    public function clearAiAnalysisCache(): void
    {
        $this->_aiAnalysisCache = false;
    }

    /**
     * Calculate the average tone score from reviewers_analytics array.
     * Returns 0 if no reviewers or all scores are null.
     *
     * @param array $aiAnalysis The ai_analysis object from the payload
     * @return float The calculated average tone score
     */
    public static function calculateToneScore(array $aiAnalysis): float
    {
        $reviewers = $aiAnalysis['reviewers_analytics'] ?? [];

        if (empty($reviewers) || !is_array($reviewers)) {
            return 0.0;
        }

        $validScores = [];
        foreach ($reviewers as $reviewer) {
            if (!is_array($reviewer)) {
                continue;
            }
            
            // Try to get tone_score from behavioral_metrics first (new schema)
            $score = $reviewer['behavioral_metrics']['tone_score'] 
                  ?? $reviewer['tone_score'] 
                  ?? null;
            
            if ($score !== null) {
                $validScores[] = (float) $score;
            }
        }

        if (empty($validScores)) {
            return 0.0;
        }

        return round(array_sum($validScores) / count($validScores), 2);
    }

    /**
     * Extract metrics from ai_analysis with sensible defaults.
     * Supports both old and new schema formats.
     *
     * @param array $aiAnalysis The ai_analysis object from the payload
     * @return array Extracted metrics ready for database insertion
     */
    public static function extractMetrics(array $aiAnalysis): array
    {
        // Business value: executive_summary.business_value_clarity (new) or business_value_clarity (old)
        $businessValue = $aiAnalysis['executive_summary']['business_value_clarity'] 
                      ?? $aiAnalysis['business_value_clarity'] 
                      ?? 0;

        // SOLID compliance: author_analytics.quality_metrics.solid_compliance (new) or solid_compliance_score (old)
        $solidCompliance = $aiAnalysis['author_analytics']['quality_metrics']['solid_compliance'] 
                        ?? $aiAnalysis['solid_compliance_score'] 
                        ?? 0;

        // Health status: executive_summary.overall_health_status (new) or health_status (old)
        $healthStatus = $aiAnalysis['executive_summary']['overall_health_status'] 
                     ?? $aiAnalysis['health_status'] 
                     ?? 'unknown';

        // Risk level: classification.risk_level (new) or risk_level (old)
        $riskLevel = $aiAnalysis['classification']['risk_level'] 
                  ?? $aiAnalysis['risk_level'] 
                  ?? 'unknown';

        // Change type: classification.change_type (new) or change_type (old)
        $changeType = $aiAnalysis['classification']['change_type'] 
                   ?? $aiAnalysis['change_type'] 
                   ?? 'unknown';

        return [
            'business_value_score' => (int) $businessValue,
            'solid_compliance_score' => (int) $solidCompliance,
            'tone_score' => self::calculateToneScore($aiAnalysis),
            'health_status' => $healthStatus,
            'risk_level' => $riskLevel,
            'change_type' => $changeType,
        ];
    }

    /**
     * Create a report from webhook payload data.
     *
     * @param Developer $developer The developer who authored the PR
     * @param array $payload The validated webhook payload
     * @return static The created PrReport instance
     */
    public static function createFromPayload(Developer $developer, array $payload): static
    {
        $aiAnalysis = $payload['ai_analysis'] ?? [];
        $metrics = self::extractMetrics($aiAnalysis);

        return static::create([
            'developer_id' => $developer->id,
            'repository' => $payload['repository'],
            'pr_number' => (string) $payload['pr_number'],
            'pr_link' => $payload['pr_link'] ?? null,
            'title' => $payload['title'] ?? null,
            'business_value_score' => $metrics['business_value_score'],
            'solid_compliance_score' => $metrics['solid_compliance_score'],
            'tone_score' => $metrics['tone_score'],
            'health_status' => $metrics['health_status'],
            'risk_level' => $metrics['risk_level'],
            'change_type' => $metrics['change_type'],
            'raw_analysis' => $aiAnalysis,
        ]);
    }

    /**
     * Get color for health status (for Filament badge).
     */
    public function getHealthColor(): string
    {
        return match($this->health_status) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical', 'danger' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get color for risk level (for Filament badge).
     */
    public function getRiskColor(): string
    {
        return match($this->risk_level) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get color for business value score (for Filament badge).
     */
    public function getBusinessValueColor(): string
    {
        $score = $this->business_value_score ?? 0;
        return match(true) {
            $score < 50 => 'danger',
            $score < 80 => 'warning',
            default => 'success',
        };
    }

    /**
     * Get color for SOLID compliance score (for Filament badge).
     */
    public function getSolidColor(): string
    {
        $score = $this->solid_compliance_score ?? 0;
        return match(true) {
            $score < 50 => 'danger',
            $score < 80 => 'warning',
            default => 'success',
        };
    }

    /**
     * Get color for tone score (for reviewer analysis).
     */
    public static function getToneScoreColor(float $score): string
    {
        return match(true) {
            $score < 5 => 'danger',
            $score < 7 => 'warning',
            default => 'success',
        };
    }

    /**
     * Get badges from raw_analysis with fallback.
     * Returns gamification_badges array or empty array.
     */
    public function getBadges(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null && $analysis->hasGamificationBadges()) {
            return array_map(fn($badge) => $badge->jsonSerialize(), $analysis->gamificationBadges);
        }
        
        // Fallback to raw data
        return $this->raw_analysis['gamification_badges'] 
            ?? $this->raw_analysis['badges'] 
            ?? [];
    }

    /**
     * Get recurring mistakes from raw_analysis with fallback.
     * Path: author_analytics.trend_analysis.recurring_mistakes
     */
    public function getRecurringMistakes(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->getRecurringMistakes();
        }
        
        // Fallback to raw data paths
        return $this->raw_analysis['author_analytics']['trend_analysis']['recurring_mistakes'] 
            ?? $this->raw_analysis['recurring_mistakes'] 
            ?? [];
    }

    /**
     * Get educational path from raw_analysis with fallback.
     * Path: author_analytics.educational_path
     */
    public function getEducationalPath(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            $path = $analysis->getEducationalPath();
            // Convert to array format for backward compatibility
            return array_map(fn($item) => $item->jsonSerialize(), $path);
        }
        
        // Fallback to raw data paths
        return $this->raw_analysis['author_analytics']['educational_path'] 
            ?? $this->raw_analysis['educational_path'] 
            ?? [];
    }

    /**
     * Get reviewers analytics from raw_analysis with fallback.
     */
    public function getReviewersAnalytics(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null && !empty($analysis->reviewersAnalytics)) {
            return array_map(fn($reviewer) => $reviewer->jsonSerialize(), $analysis->reviewersAnalytics);
        }
        
        // Fallback to raw data
        return $this->raw_analysis['reviewers_analytics'] ?? [];
    }

    /**
     * Get refactoring suggestions from raw_analysis with fallback.
     * Path: technical_debt_analysis.suggestions_for_refactor
     */
    public function getRefactoringSuggestions(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->getRefactorSuggestions();
        }
        
        // Fallback to raw data paths
        return $this->raw_analysis['technical_debt_analysis']['suggestions_for_refactor'] 
            ?? $this->raw_analysis['suggestions_for_refactor'] 
            ?? [];
    }

    /**
     * Check if over-engineering is detected.
     * Path: technical_debt_analysis.over_engineering_detected
     */
    public function isOverEngineered(): bool
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->isOverEngineered();
        }
        
        // Fallback to raw data paths
        return (bool) ($this->raw_analysis['technical_debt_analysis']['over_engineering_detected'] 
            ?? $this->raw_analysis['over_engineering'] 
            ?? false);
    }

    /**
     * Get test coverage quality from raw_analysis.
     * Path: author_analytics.quality_metrics.test_coverage_quality
     */
    public function getTestCoverageQuality(): ?string
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->authorAnalytics->qualityMetrics->testCoverageQuality;
        }
        
        return $this->raw_analysis['author_analytics']['quality_metrics']['test_coverage_quality'] 
            ?? $this->raw_analysis['test_coverage_quality'] 
            ?? null;
    }

    /**
     * Get velocity metrics from raw_analysis.
     * Path: author_analytics.velocity_metrics
     */
    public function getVelocityMetrics(): ?array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null && $analysis->authorAnalytics->velocityMetrics !== null) {
            return $analysis->authorAnalytics->velocityMetrics->jsonSerialize();
        }
        
        return $this->raw_analysis['author_analytics']['velocity_metrics'] 
            ?? $this->raw_analysis['velocity_metrics'] 
            ?? null;
    }

    /**
     * Get the final verdict from management decision assist.
     */
    public function getFinalVerdict(): string
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->getFinalVerdict();
        }
        
        return $this->raw_analysis['management_decision_assist']['final_verdict_fa'] 
            ?? $this->raw_analysis['final_verdict'] 
            ?? '';
    }

    /**
     * Check if HR attention is required.
     */
    public function requiresHrAttention(): bool
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->requiresHrAttention();
        }
        
        return (bool) ($this->raw_analysis['management_decision_assist']['hr_flag'] 
            ?? $this->raw_analysis['hr_flag'] 
            ?? false);
    }

    /**
     * Get title summary from executive summary.
     */
    public function getTitleSummary(): string
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->executiveSummary->titleSummary;
        }
        
        return $this->raw_analysis['executive_summary']['title_summary'] 
            ?? $this->title 
            ?? '';
    }

    /**
     * Check if PR is blocking.
     */
    public function isBlocking(): bool
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis !== null) {
            return $analysis->isBlocking();
        }
        
        return (bool) ($this->raw_analysis['classification']['is_blocking'] 
            ?? $this->raw_analysis['is_blocking'] 
            ?? false);
    }
}
