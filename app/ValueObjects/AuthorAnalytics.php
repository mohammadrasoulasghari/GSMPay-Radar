<?php

namespace App\ValueObjects;

use JsonSerializable;

class AuthorAnalytics implements JsonSerializable
{
    public function __construct(
        public readonly string $skillLevel,
        public readonly array $focusAreas,
        public readonly array $strengths,
        public readonly array $improvementAreas,
        public readonly string $identity,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            skillLevel: $data['skill_level'] ?? 'unknown',
            focusAreas: $data['focus_areas'] ?? [],
            strengths: $data['strengths'] ?? [],
            improvementAreas: $data['improvement_areas'] ?? [],
            identity: $data['identity'] ?? 'unknown',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'skill_level' => $this->skillLevel,
            'focus_areas' => $this->focusAreas,
            'strengths' => $this->strengths,
            'improvement_areas' => $this->improvementAreas,
            'identity' => $this->identity,
        ];
    }
}