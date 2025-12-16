<?php

namespace App\ValueObjects;

use JsonSerializable;

class TechnicalDebtAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly int $score,
        public readonly int $lineCount,
        public readonly int $complexityCount,
        public readonly int $duplicateCount,
        public readonly array $recommendations,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            score: (int) ($data['score'] ?? 0),
            lineCount: (int) ($data['line_count'] ?? 0),
            complexityCount: (int) ($data['complexity_count'] ?? 0),
            duplicateCount: (int) ($data['duplicate_count'] ?? 0),
            recommendations: $data['recommendations'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'score' => $this->score,
            'line_count' => $this->lineCount,
            'complexity_count' => $this->complexityCount,
            'duplicate_count' => $this->duplicateCount,
            'recommendations' => $this->recommendations,
        ];
    }

    public function getRiskLevel(): string
    {
        return match (true) {
            $this->score >= 80 => 'low',
            $this->score >= 60 => 'medium',
            $this->score >= 40 => 'high',
            default => 'critical',
        };
    }

    public function getRiskColor(): string
    {
        return match ($this->getRiskLevel()) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger',
        };
    }

    public function getTotalIssues(): int
    {
        return $this->lineCount + $this->complexityCount + $this->duplicateCount;
    }
}