<?php

namespace App\ValueObjects;

use JsonSerializable;

class GamificationBadge implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $icon,
        public readonly string $requirement,
        public readonly ?string $description = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            type: $data['type'] ?? 'achievement',
            icon: $data['icon'] ?? '🏆',
            requirement: $data['requirement'] ?? '',
            description: $data['description'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon,
            'requirement' => $this->requirement,
            'description' => $this->description,
        ];
    }

    public function getTypeColor(): string
    {
        return match ($this->type) {
            'achievement' => 'success',
            'milestone' => 'warning',
            'streak' => 'info',
            'special' => 'primary',
            default => 'gray',
        };
    }

    public function getDisplayName(): string
    {
        return $this->icon . ' ' . $this->name;
    }
}