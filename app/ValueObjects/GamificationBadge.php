<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Single gamification badge.
 * Required fields: badge_name, recipient, type, reason_fa
 */
class GamificationBadge implements JsonSerializable
{
    public const VALID_BADGES = [
        'The Sniper',
        'The Architect',
        'Clean Coder',
        'Fast Responder',
        'Ghost',
        'Nitpicker',
        'Teacher',
    ];

    public const VALID_TYPES = ['positive', 'negative', 'neutral'];

    public function __construct(
        public readonly string $badgeName,
        public readonly string $recipient,
        public readonly string $type,
        public readonly string $reasonFa,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                badgeName: 'unknown',
                recipient: 'unknown',
                type: 'neutral',
                reasonFa: '',
            );
        }

        return new self(
            badgeName: $data['badge_name'] ?? 'unknown',
            recipient: $data['recipient'] ?? 'unknown',
            type: $data['type'] ?? 'neutral',
            reasonFa: $data['reason_fa'] ?? '',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'badge_name' => $this->badgeName,
            'recipient' => $this->recipient,
            'type' => $this->type,
            'reason_fa' => $this->reasonFa,
        ];
    }

    public function getTypeColor(): string
    {
        return match ($this->type) {
            'positive' => 'success',
            'negative' => 'danger',
            'neutral' => 'warning',
            default => 'gray',
        };
    }

    public function getIcon(): string
    {
        return match ($this->badgeName) {
            'The Sniper' => '🎯',
            'The Architect' => '🏛️',
            'Clean Coder' => '✨',
            'Fast Responder' => '⚡',
            'Ghost' => '👻',
            'Nitpicker' => '🔍',
            'Teacher' => '📚',
            default => '🏆',
        };
    }

    public function getDisplayName(): string
    {
        return $this->getIcon() . ' ' . $this->badgeName;
    }
}
