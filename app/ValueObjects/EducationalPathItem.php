<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Single educational path item.
 */
class EducationalPathItem implements JsonSerializable
{
    public function __construct(
        public readonly string $topic,
        public readonly string $reason,
        public readonly ?string $link = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                topic: '',
                reason: '',
                link: null,
            );
        }

        return new self(
            topic: $data['topic'] ?? '',
            reason: $data['reason'] ?? '',
            link: $data['link'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'topic' => $this->topic,
            'reason' => $this->reason,
            'link' => $this->link,
        ], fn($v) => $v !== null && $v !== '');
    }
}
