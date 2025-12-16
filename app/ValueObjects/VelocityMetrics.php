<?php

namespace App\ValueObjects;

use JsonSerializable;

class VelocityMetrics implements JsonSerializable
{
    public function __construct(
        public readonly string $velocity,
        public readonly int $commitFrequency,
        public readonly int $linesChanged,
        public readonly int $filesChanged,
        public readonly ?float $avgResponseTimeHours = null,
        public readonly ?int $reworkCycles = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            velocity: $data['velocity'] ?? 'medium',
            commitFrequency: (int) ($data['commit_frequency'] ?? 0),
            linesChanged: (int) ($data['lines_changed'] ?? 0),
            filesChanged: (int) ($data['files_changed'] ?? 0),
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
        return [
            'velocity' => $this->velocity,
            'commit_frequency' => $this->commitFrequency,
            'lines_changed' => $this->linesChanged,
            'files_changed' => $this->filesChanged,
            'avg_response_time_hours' => $this->avgResponseTimeHours,
            'rework_cycles' => $this->reworkCycles,
        ];
    }

    public function getResponseTimeColor(): string
    {
        if ($this->avgResponseTimeHours === null) return 'gray';

        return match (true) {
            $this->avgResponseTimeHours <= 4 => 'success',
            $this->avgResponseTimeHours <= 24 => 'warning',
            default => 'danger',
        };
    }
}