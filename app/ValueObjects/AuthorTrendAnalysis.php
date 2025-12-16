<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Trend analysis for author analytics.
 */
class AuthorTrendAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly string $improvementStatus,
        public readonly array $recurringMistakes,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                improvementStatus: 'stable',
                recurringMistakes: [],
            );
        }

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

    public function getImprovementColor(): string
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
