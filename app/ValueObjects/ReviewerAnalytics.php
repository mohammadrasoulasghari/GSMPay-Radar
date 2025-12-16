<?php

namespace App\ValueObjects;

use JsonSerializable;

class ReviewerAnalytics implements JsonSerializable
{
    public function __construct(
        public readonly string $reviewerLogin,
        public readonly EngagementMetrics $engagementMetrics,
        public readonly BehavioralMetrics $behavioralMetrics,
        public readonly ?CategoryBreakdown $categoryBreakdown,
        public readonly FeedbackSamples $feedbackSamples,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            reviewerLogin: $data['reviewer_login'] ?? 'unknown',
            engagementMetrics: EngagementMetrics::fromArray($data['engagement_metrics'] ?? []),
            behavioralMetrics: BehavioralMetrics::fromArray($data['behavioral_metrics'] ?? []),
            categoryBreakdown: isset($data['category_breakdown'])
                ? CategoryBreakdown::fromArray($data['category_breakdown'])
                : null,
            feedbackSamples: FeedbackSamples::fromArray($data['feedback_samples'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'reviewer_login' => $this->reviewerLogin,
            'engagement_metrics' => $this->engagementMetrics,
            'behavioral_metrics' => $this->behavioralMetrics,
            'category_breakdown' => $this->categoryBreakdown,
            'feedback_samples' => $this->feedbackSamples,
        ];
    }
}