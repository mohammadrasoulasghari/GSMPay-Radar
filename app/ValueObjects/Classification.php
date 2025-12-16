<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Classification of the PR.
 * Required fields: change_type, risk_level, is_blocking
 */
class Classification implements JsonSerializable
{
    public function __construct(
        public readonly string $changeType,
        public readonly string $riskLevel,
        public readonly bool $isBlocking,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                changeType: 'unknown',
                riskLevel: 'unknown',
                isBlocking: false,
            );
        }

        return new self(
            changeType: $data['change_type'] ?? 'unknown',
            riskLevel: $data['risk_level'] ?? 'unknown',
            isBlocking: (bool) ($data['is_blocking'] ?? false),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'change_type' => $this->changeType,
            'risk_level' => $this->riskLevel,
            'is_blocking' => $this->isBlocking,
        ];
    }

    public function getRiskColor(): string
    {
        return match ($this->riskLevel) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'gray',
        };
    }

    public function getChangeTypeColor(): string
    {
        return match ($this->changeType) {
            'feature' => 'success',
            'bugfix' => 'warning',
            'hotfix' => 'danger',
            'refactor' => 'info',
            'chore' => 'gray',
            'documentation' => 'primary',
            default => 'gray',
        };
    }
}
