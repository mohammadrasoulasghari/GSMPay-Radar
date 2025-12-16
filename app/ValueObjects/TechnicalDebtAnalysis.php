<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Technical debt analysis.
 */
class TechnicalDebtAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly string $addedDebtLevel,
        public readonly bool $overEngineeringDetected,
        public readonly array $suggestionsForRefactor,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                addedDebtLevel: 'none',
                overEngineeringDetected: false,
                suggestionsForRefactor: [],
            );
        }

        return new self(
            addedDebtLevel: $data['added_debt_level'] ?? 'none',
            overEngineeringDetected: (bool) ($data['over_engineering_detected'] ?? false),
            suggestionsForRefactor: $data['suggestions_for_refactor'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'added_debt_level' => $this->addedDebtLevel,
            'over_engineering_detected' => $this->overEngineeringDetected,
            'suggestions_for_refactor' => $this->suggestionsForRefactor,
        ];
    }

    public function getDebtLevelColor(): string
    {
        return match ($this->addedDebtLevel) {
            'none' => 'success',
            'low' => 'warning',
            'high' => 'danger',
            default => 'gray',
        };
    }

    public function hasSuggestions(): bool
    {
        return !empty($this->suggestionsForRefactor);
    }
}
