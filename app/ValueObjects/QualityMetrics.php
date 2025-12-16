<?php

namespace App\ValueObjects;

use JsonSerializable;

class QualityMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $score,
        public readonly int $codeReadability,
        public readonly int $testCoverage,
        public readonly int $solidCompliance,
        public readonly int $performanceScore,
        public readonly string $bugPotential, // 'low', 'medium', 'high'
        public readonly ?string $testCoverageQuality = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            score: (int) ($data['score'] ?? 0),
            codeReadability: (int) ($data['code_readability'] ?? 0),
            testCoverage: (int) ($data['test_coverage'] ?? 0),
            solidCompliance: (int) ($data['solid_compliance'] ?? 0),
            performanceScore: (int) ($data['performance_score'] ?? 0),
            bugPotential: $data['bug_potential'] ?? 'unknown',
            testCoverageQuality: $data['test_coverage_quality'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'score' => $this->score,
            'code_readability' => $this->codeReadability,
            'test_coverage' => $this->testCoverage,
            'solid_compliance' => $this->solidCompliance,
            'performance_score' => $this->performanceScore,
            'bug_potential' => $this->bugPotential,
            'test_coverage_quality' => $this->testCoverageQuality,
        ];
    }

    public function getSolidComplianceColor(): string
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

    public function getQualityColor(): string
    {
        return match (true) {
            $this->score >= 80 => 'success',
            $this->score >= 60 => 'warning',
            $this->score >= 40 => 'danger',
            default => 'gray',
        };
    }

    public function getSolidColor(): string
    {
        return $this->getSolidComplianceColor();
    }

    public function getTestCoverageColor(): string
    {
        return match (true) {
            $this->testCoverage >= 80 => 'success',
            $this->testCoverage >= 60 => 'warning',
            default => 'danger',
        };
    }

    public function getReadabilityColor(): string
    {
        return match (true) {
            $this->codeReadability >= 80 => 'success',
            $this->codeReadability >= 60 => 'warning',
            default => 'danger',
        };
    }
}