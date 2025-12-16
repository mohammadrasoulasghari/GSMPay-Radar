<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Author analytics - analysis of the code author's performance.
 * Required fields: identity, quality_metrics, trend_analysis
 */
class AuthorAnalytics implements JsonSerializable
{
    public function __construct(
        public readonly string $identity,
        public readonly AuthorQualityMetrics $qualityMetrics,
        public readonly AuthorTrendAnalysis $trendAnalysis,
        public readonly ?AuthorVelocityMetrics $velocityMetrics = null,
        public readonly array $educationalPath = [], // EducationalPathItem[]
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                identity: 'unknown',
                qualityMetrics: AuthorQualityMetrics::fromArray(null),
                trendAnalysis: AuthorTrendAnalysis::fromArray(null),
                velocityMetrics: null,
                educationalPath: [],
            );
        }

        $educationalPath = [];
        if (!empty($data['educational_path']) && is_array($data['educational_path'])) {
            $educationalPath = array_map(
                fn($item) => EducationalPathItem::fromArray($item),
                $data['educational_path']
            );
        }

        return new self(
            identity: $data['identity'] ?? 'unknown',
            qualityMetrics: AuthorQualityMetrics::fromArray($data['quality_metrics'] ?? null),
            trendAnalysis: AuthorTrendAnalysis::fromArray($data['trend_analysis'] ?? null),
            velocityMetrics: isset($data['velocity_metrics']) 
                ? AuthorVelocityMetrics::fromArray($data['velocity_metrics']) 
                : null,
            educationalPath: $educationalPath,
        );
    }

    public function jsonSerialize(): array
    {
        $result = [
            'identity' => $this->identity,
            'quality_metrics' => $this->qualityMetrics,
            'trend_analysis' => $this->trendAnalysis,
        ];

        if ($this->velocityMetrics !== null) {
            $result['velocity_metrics'] = $this->velocityMetrics;
        }

        if (!empty($this->educationalPath)) {
            $result['educational_path'] = $this->educationalPath;
        }

        return $result;
    }

    public function getEducationalPath(): array
    {
        return $this->educationalPath;
    }

    public function getRecurringMistakes(): array
    {
        return $this->trendAnalysis->recurringMistakes;
    }

    public function hasEducationalPath(): bool
    {
        return !empty($this->educationalPath);
    }
}
