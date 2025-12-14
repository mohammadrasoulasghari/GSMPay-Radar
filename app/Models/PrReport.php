<?php

namespace App\Models;

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
            'business_value_score' => (int) ($aiAnalysis['business_value_clarity'] ?? 0),
            'solid_compliance_score' => (int) ($aiAnalysis['solid_compliance_score'] ?? 0),
            'tone_score' => self::calculateToneScore($aiAnalysis),
            'health_status' => $aiAnalysis['health_status'] ?? 'unknown',
            'risk_level' => $aiAnalysis['risk_level'] ?? 'unknown',
            'change_type' => $aiAnalysis['change_type'] ?? 'unknown',
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
}
