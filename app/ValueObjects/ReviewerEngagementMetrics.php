<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Engagement metrics for reviewer analytics.
 */
class ReviewerEngagementMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $totalComments,
        public readonly float $nitpickingRatio,
        public readonly string $responseSpeedRating,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                totalComments: 0,
                nitpickingRatio: 0.0,
                responseSpeedRating: 'normal',
            );
        }

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

    public function getResponseSpeedColor(): string
    {
        return match ($this->responseSpeedRating) {
            'fast' => 'success',
            'normal' => 'warning',
            'slow' => 'danger',
            default => 'gray',
        };
    }

    public function getNitpickingColor(): string
    {
        return match (true) {
            $this->nitpickingRatio <= 0.2 => 'success',
            $this->nitpickingRatio <= 0.4 => 'warning',
            default => 'danger',
        };
    }
}
