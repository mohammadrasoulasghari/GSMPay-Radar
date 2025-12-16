<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Management decision assist.
 * Required fields: final_verdict_fa, performance_review_topic
 */
class ManagementDecisionAssist implements JsonSerializable
{
    public function __construct(
        public readonly string $finalVerdictFa,
        public readonly string $performanceReviewTopic,
        public readonly bool $hrFlag,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                finalVerdictFa: '',
                performanceReviewTopic: '',
                hrFlag: false,
            );
        }

        return new self(
            finalVerdictFa: $data['final_verdict_fa'] ?? '',
            performanceReviewTopic: $data['performance_review_topic'] ?? '',
            hrFlag: (bool) ($data['hr_flag'] ?? false),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'final_verdict_fa' => $this->finalVerdictFa,
            'performance_review_topic' => $this->performanceReviewTopic,
            'hr_flag' => $this->hrFlag,
        ];
    }

    public function getHrFlagColor(): string
    {
        return $this->hrFlag ? 'danger' : 'success';
    }

    public function hasVerdict(): bool
    {
        return !empty($this->finalVerdictFa);
    }

    public function hasPerformanceReviewTopic(): bool
    {
        return !empty($this->performanceReviewTopic);
    }
}
