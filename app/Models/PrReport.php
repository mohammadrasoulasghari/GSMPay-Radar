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

    /**
     * Get the developer that owns this report.
     */
    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    /**
     * Get the AI analysis as a Value Object.
     */
    public function getAiAnalysis(): ?AiAnalysis
    {
        if (empty($this->raw_analysis) || !is_array($this->raw_analysis)) {
            return null;
        }

        return AiAnalysis::fromArray($this->raw_analysis);
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

        if (empty($reviewers)) {
            return 0.0;
        }

        $toneScores = array_map(
            fn($reviewer) => (float) ($reviewer['tone_score'] ?? 0),
            $reviewers
        );

        $validScores = array_filter($toneScores, fn($score) => $score !== null);

        if (count($validScores) === 0) {
            return 0.0;
        }

        return round(array_sum($validScores) / count($validScores), 2);
    }

    /**
     * Extract metrics from ai_analysis with sensible defaults.
     *
     * @param array $aiAnalysis The ai_analysis object from the payload
     * @return array Extracted metrics ready for database insertion
     */
    public static function extractMetrics(array $aiAnalysis): array
    {
        return [
            'business_value_score' => (int) ($aiAnalysis['classification']['business_value'] ?? 
                                           $aiAnalysis['business_value_clarity'] ?? 0),
            'solid_compliance_score' => (int) ($aiAnalysis['quality_metrics']['solid_compliance'] ?? 
                                             $aiAnalysis['solid_compliance_score'] ?? 0),
            'tone_score' => self::calculateToneScore($aiAnalysis),
            'health_status' => $aiAnalysis['classification']['health_status'] ?? 
                             $aiAnalysis['health_status'] ?? 'unknown',
            'risk_level' => $aiAnalysis['classification']['risk_level'] ?? 
                          $aiAnalysis['risk_level'] ?? 'unknown',
            'change_type' => $aiAnalysis['classification']['change_type'] ?? 
                           $aiAnalysis['change_type'] ?? 'unknown',
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
     */
    public function getBadges(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis && $analysis->hasGamificationBadges()) {
            return array_map(fn($badge) => $badge->jsonSerialize(), $analysis->gamificationBadges);
        }
        
        return $this->raw_analysis['badges'] ?? [];
    }

    /**
     * Get recurring mistakes from raw_analysis with fallback.
     */
    public function getRecurringMistakes(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->educationalRecommendation->recurringMistakes;
        }
        
        return $this->raw_analysis['recurring_mistakes'] ?? [];
    }

    /**
     * Get educational path from raw_analysis with fallback.
     */
    public function getEducationalPath(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->educationalRecommendation->educationalPath;
        }
        
        return $this->raw_analysis['educational_path'] ?? [];
    }

    /**
     * Get reviewers analytics from raw_analysis with fallback.
     */
    public function getReviewersAnalytics(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return array_map(fn($reviewer) => $reviewer->jsonSerialize(), $analysis->reviewersAnalytics);
        }
        
        return $this->raw_analysis['reviewers_analytics'] ?? [];
    }

    /**
     * Get refactoring suggestions from raw_analysis with fallback.
     */
    public function getRefactoringSuggestions(): array
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->educationalRecommendation->suggestions;
        }
        
        return $this->raw_analysis['suggestions_for_refactor'] ?? [];
    }

    /**
     * Check if over-engineering is detected.
     */
    public function isOverEngineered(): bool
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->classification->overEngineering;
        }
        
        return ($this->raw_analysis['over_engineering'] ?? false) === true;
    }

    /**
     * Get test coverage percentage from raw_analysis.
     */
    public function getTestCoverage(): ?int
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->qualityMetrics->testCoverage;
        }
        
        return $this->raw_analysis['test_coverage_percentage'] ?? null;
    }

    /**
     * Get velocity from raw_analysis.
     */
    public function getVelocity(): ?string
    {
        $analysis = $this->getAiAnalysis();
        if ($analysis) {
            return $analysis->velocityMetrics->velocity;
        }
        
        return $this->raw_analysis['velocity'] ?? null;
    }
}
