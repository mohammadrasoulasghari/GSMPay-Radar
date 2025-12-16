<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Executive summary of the PR analysis.
 * Required fields: title_summary, business_value_clarity, overall_health_status
 */
class ExecutiveSummary implements JsonSerializable
{
    public function __construct(
        public readonly string $titleSummary,
        public readonly int $businessValueClarity,
        public readonly string $overallHealthStatus,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                titleSummary: '',
                businessValueClarity: 0,
                overallHealthStatus: 'unknown',
            );
        }

        return new self(
            titleSummary: $data['title_summary'] ?? '',
            businessValueClarity: (int) ($data['business_value_clarity'] ?? 0),
            overallHealthStatus: $data['overall_health_status'] ?? 'unknown',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'title_summary' => $this->titleSummary,
            'business_value_clarity' => $this->businessValueClarity,
            'overall_health_status' => $this->overallHealthStatus,
        ];
    }

    public function getHealthColor(): string
    {
        return match ($this->overallHealthStatus) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    public function getBusinessValueColor(): string
    {
        return match (true) {
            $this->businessValueClarity >= 80 => 'success',
            $this->businessValueClarity >= 50 => 'warning',
            default => 'danger',
        };
    }
}
