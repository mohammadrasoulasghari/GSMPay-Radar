<?php

namespace App\ValueObjects;

use JsonSerializable;

class Classification implements JsonSerializable
{
    public function __construct(
        public readonly string $changeType, // 'feature', 'bugfix', 'refactor', 'hotfix', 'chore', 'documentation'
        public readonly string $complexity, // 'simple', 'medium', 'complex'
        public readonly string $riskLevel, // 'low', 'medium', 'high'
        public readonly int $businessValue, // 0-100
        public readonly string $healthStatus, // 'healthy', 'warning', 'critical'
        public readonly bool $overEngineering,
        public readonly bool $isBlocking,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            changeType: $data['change_type'] ?? 'unknown',
            complexity: $data['complexity'] ?? 'medium',
            riskLevel: $data['risk_level'] ?? 'unknown',
            businessValue: (int) ($data['business_value'] ?? 0),
            healthStatus: $data['health_status'] ?? 'unknown',
            overEngineering: (bool) ($data['over_engineering'] ?? false),
            isBlocking: (bool) ($data['is_blocking'] ?? false),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'change_type' => $this->changeType,
            'complexity' => $this->complexity,
            'risk_level' => $this->riskLevel,
            'business_value' => $this->businessValue,
            'health_status' => $this->healthStatus,
            'over_engineering' => $this->overEngineering,
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

    public function getRiskLevelColor(): string
    {
        return $this->getRiskColor();
    }

    public function getHealthColor(): string
    {
        return match ($this->healthStatus) {
            'healthy' => 'success',
            'warning' => 'warning',
            'critical' => 'danger',
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

    public function getComplexityColor(): string
    {
        return match ($this->complexity) {
            'simple' => 'success',
            'medium' => 'warning',
            'complex' => 'danger',
            default => 'gray',
        };
    }

    public function getBusinessValueColor(): string
    {
        return match (true) {
            $this->businessValue >= 80 => 'success',
            $this->businessValue >= 60 => 'warning',
            $this->businessValue >= 40 => 'danger',
            default => 'gray',
        };
    }
}