<?php

namespace App\ValueObjects;

use JsonSerializable;

class TrendAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly string $improvementStatus, // 'improved', 'stable', 'declined'
        public readonly array $recurringMistakes, // string[]
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            improvementStatus: $data['improvement_status'] ?? 'stable',
            recurringMistakes: $data['recurring_mistakes'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'improvement_status' => $this->improvementStatus,
            'recurring_mistakes' => $this->recurringMistakes,
        ];
    }

    public function getImprovementStatusColor(): string
    {
        return match ($this->improvementStatus) {
            'improved' => 'success',
            'stable' => 'warning',
            'declined' => 'danger',
            default => 'gray',
        };
    }

    public function hasRecurringMistakes(): bool
    {
        return !empty($this->recurringMistakes);
    }
}