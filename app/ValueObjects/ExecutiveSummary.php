<?php

namespace App\ValueObjects;

use JsonSerializable;

class ExecutiveSummary implements JsonSerializable
{
    public function __construct(
        public readonly string $titleSummary,
        public readonly int $businessValueClarity,
        public readonly string $overallHealthStatus, // 'healthy', 'warning', 'critical'
    ) {}

    public static function fromArray(array $data): self
    {
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

    public function getBusinessValueColor(): string
    {
        return match (true) {
            $this->businessValueClarity >= 80 => 'success',
            $this->businessValueClarity >= 50 => 'warning',
            default => 'danger',
        };
    }

    public function getHealthStatusColor(): string
    {
        return match ($this->overallHealthStatus) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };
    }
}