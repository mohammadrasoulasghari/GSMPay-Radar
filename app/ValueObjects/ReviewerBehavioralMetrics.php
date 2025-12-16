<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Behavioral metrics for reviewer analytics.
 */
class ReviewerBehavioralMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $toneScore,
        public readonly int $mentorshipScore,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                toneScore: 0,
                mentorshipScore: 0,
            );
        }

        return new self(
            toneScore: (int) ($data['tone_score'] ?? 0),
            mentorshipScore: (int) ($data['mentorship_score'] ?? 0),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'tone_score' => $this->toneScore,
            'mentorship_score' => $this->mentorshipScore,
        ];
    }

    public function getToneColor(): string
    {
        return match (true) {
            $this->toneScore >= 80 => 'success',
            $this->toneScore >= 50 => 'warning',
            default => 'danger',
        };
    }

    public function getMentorshipColor(): string
    {
        return match (true) {
            $this->mentorshipScore >= 80 => 'success',
            $this->mentorshipScore >= 50 => 'warning',
            default => 'danger',
        };
    }
}
