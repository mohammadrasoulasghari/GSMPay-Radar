<?php

namespace App\ValueObjects;

use JsonSerializable;

class EngagementMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $totalComments,
        public readonly float $nitpickingRatio, // 0.0 to 1.0
        public readonly string $responseSpeedRating, // 'fast', 'normal', 'slow'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            totalComments: (int) ($data['total_comments'] ?? 0),
            nitpickingRatio: (float) ($data['nitpicking_ratio'] ?? 0.0),
            responseSpeedRating: $data['response_speed_rating'] ?? 'normal',
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'total_comments' => $this->totalComments,
            'nitpicking_ratio' => $this->nitpickingRatio,
            'response_speed_rating' => $this->responseSpeedRating,
        ];
    }

    public function getNitpickingColor(): string
    {
        return match (true) {
            $this->nitpickingRatio <= 0.3 => 'success',
            $this->nitpickingRatio <= 0.6 => 'warning',
            default => 'danger',
        };
    }

    public function getResponseSpeedColor(): string
    {
        return match ($this->responseSpeedRating) {
            'fast' => 'success',
            'normal' => 'warning',
            'slow' => 'danger',
            default => 'gray',
        };
    }
}