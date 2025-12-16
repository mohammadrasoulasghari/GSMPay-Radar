<?php

namespace App\ValueObjects;

use JsonSerializable;

class BehavioralMetrics implements JsonSerializable
{
    public function __construct(
        public readonly int $toneScore, // 0 to 100
        public readonly int $mentorshipScore, // 0 to 100
    ) {}

    public static function fromArray(array $data): self
    {
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

    public function getToneScoreColor(): string
    {
        return match (true) {
            $this->toneScore >= 80 => 'success',
            $this->toneScore >= 60 => 'warning',
            default => 'danger',
        };
    }

    public function getMentorshipColor(): string
    {
        return match (true) {
            $this->mentorshipScore >= 70 => 'success',
            $this->mentorshipScore >= 40 => 'warning',
            default => 'danger',
        };
    }
}