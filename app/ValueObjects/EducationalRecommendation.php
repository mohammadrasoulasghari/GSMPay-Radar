<?php

namespace App\ValueObjects;

use JsonSerializable;

class EducationalRecommendation implements JsonSerializable
{
    public function __construct(
        public readonly string $topic,
        public readonly string $reason,
        public readonly ?string $link = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            topic: $data['topic'] ?? '',
            reason: $data['reason'] ?? '',
            link: $data['link'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'topic' => $this->topic,
            'reason' => $this->reason,
            'link' => $this->link,
        ];
    }
}