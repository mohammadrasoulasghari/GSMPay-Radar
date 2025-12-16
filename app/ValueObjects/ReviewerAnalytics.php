<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Single reviewer analytics item.
 * Required fields: reviewer_login, engagement_metrics, behavioral_metrics, feedback_samples
 */
class ReviewerAnalytics implements JsonSerializable
{
    public function __construct(
        public readonly string $reviewerLogin,
        public readonly ReviewerEngagementMetrics $engagementMetrics,
        public readonly ReviewerBehavioralMetrics $behavioralMetrics,
        public readonly ReviewerFeedbackSamples $feedbackSamples,
        public readonly ?ReviewerCategoryBreakdown $categoryBreakdown = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self(
                reviewerLogin: 'unknown',
                engagementMetrics: ReviewerEngagementMetrics::fromArray(null),
                behavioralMetrics: ReviewerBehavioralMetrics::fromArray(null),
                feedbackSamples: ReviewerFeedbackSamples::fromArray(null),
                categoryBreakdown: null,
            );
        }

        return new self(
            reviewerLogin: $data['reviewer_login'] ?? 'unknown',
            engagementMetrics: ReviewerEngagementMetrics::fromArray($data['engagement_metrics'] ?? null),
            behavioralMetrics: ReviewerBehavioralMetrics::fromArray($data['behavioral_metrics'] ?? null),
            feedbackSamples: ReviewerFeedbackSamples::fromArray($data['feedback_samples'] ?? null),
            categoryBreakdown: isset($data['category_breakdown']) 
                ? ReviewerCategoryBreakdown::fromArray($data['category_breakdown']) 
                : null,
        );
    }

    public function jsonSerialize(): array
    {
        $result = [
            'reviewer_login' => $this->reviewerLogin,
            'engagement_metrics' => $this->engagementMetrics,
            'behavioral_metrics' => $this->behavioralMetrics,
            'feedback_samples' => $this->feedbackSamples,
        ];

        if ($this->categoryBreakdown !== null) {
            $result['category_breakdown'] = $this->categoryBreakdown;
        }

        return $result;
    }

    public function getToneScore(): int
    {
        return $this->behavioralMetrics->toneScore;
    }

    public function getToneColor(): string
    {
        return $this->behavioralMetrics->getToneColor();
    }
}
