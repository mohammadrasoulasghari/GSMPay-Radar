<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Quality metrics for author analytics.
 */
class AuthorQualityMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $solidCompliance,
        public readonly string $bugPotential,
        public readonly ?string $testCoverageQuality = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                solidCompliance: 0,
                bugPotential: 'unknown',
                testCoverageQuality: null,
            );
        }

        return new self(
            solidCompliance: (int) ($data['solid_compliance'] ?? 0),
            bugPotential: $data['bug_potential'] ?? 'unknown',
            testCoverageQuality: $data['test_coverage_quality'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'solid_compliance' => $this->solidCompliance,
            'bug_potential' => $this->bugPotential,
            'test_coverage_quality' => $this->testCoverageQuality,
        ], fn($v) => $v !== null);
    }

    public function getSolidColor(): string
    {
        return match (true) {
            $this->solidCompliance >= 80 => 'success',
            $this->solidCompliance >= 50 => 'warning',
            default => 'danger',
        };
    }

    public function getBugPotentialColor(): string
    {
        return match ($this->bugPotential) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'gray',
        };
    }
}
