<?php

namespace App\ValueObjects;

use JsonSerializable;

class ManagementDecisionAssist implements JsonSerializable
{
    public function __construct(
        public readonly bool $approved,
        public readonly float $confidence,
        public readonly array $priorities,
        public readonly string $recommendation,
        public readonly array $risks,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            approved: (bool) ($data['approved'] ?? false),
            confidence: (float) ($data['confidence'] ?? 0.0),
            priorities: $data['priorities'] ?? [],
            recommendation: $data['recommendation'] ?? '',
            risks: $data['risks'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'approved' => $this->approved,
            'confidence' => $this->confidence,
            'priorities' => $this->priorities,
            'recommendation' => $this->recommendation,
            'risks' => $this->risks,
        ];
    }

    public function getConfidenceLevel(): string
    {
        return match (true) {
            $this->confidence >= 0.9 => 'high',
            $this->confidence >= 0.7 => 'medium',
            $this->confidence >= 0.5 => 'low',
            default => 'very_low',
        };
    }

    public function getConfidenceColor(): string
    {
        return match ($this->getConfidenceLevel()) {
            'high' => 'success',
            'medium' => 'info',
            'low' => 'warning',
            'very_low' => 'danger',
        };
    }

    public function getRecommendationColor(): string
    {
        return $this->approved ? 'success' : 'danger';
    }
}