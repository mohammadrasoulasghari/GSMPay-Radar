<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Velocity metrics for author analytics.
 */
class AuthorVelocityMetrics implements JsonSerializable
{
    public function __construct(
        public readonly ?float $avgResponseTimeHours = null,
        public readonly ?int $reworkCycles = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self();
        }

        return new self(
            avgResponseTimeHours: isset($data['avg_response_time_hours']) 
                ? (float) $data['avg_response_time_hours'] 
                : null,
            reworkCycles: isset($data['rework_cycles']) 
                ? (int) $data['rework_cycles'] 
                : null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'avg_response_time_hours' => $this->avgResponseTimeHours,
            'rework_cycles' => $this->reworkCycles,
        ], fn($v) => $v !== null);
    }

    public function getResponseSpeedColor(): string
    {
        if ($this->avgResponseTimeHours === null) {
            return 'gray';
        }

        return match (true) {
            $this->avgResponseTimeHours <= 4 => 'success',
            $this->avgResponseTimeHours <= 24 => 'warning',
            default => 'danger',
        };
    }
}
