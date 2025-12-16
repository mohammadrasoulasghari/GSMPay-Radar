<?php

namespace App\ValueObjects;

use JsonSerializable;

class AiAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly MetaData $metaData,
        public readonly ExecutiveSummary $executiveSummary,
        public readonly Classification $classification,
        public readonly AuthorAnalytics $authorAnalytics,
        public readonly QualityMetrics $qualityMetrics,
        public readonly VelocityMetrics $velocityMetrics,
        public readonly TrendAnalysis $trendAnalysis,
        public readonly EducationalRecommendation $educationalRecommendation,
        public readonly array $reviewersAnalytics, // ReviewerAnalytics[]
        public readonly EngagementMetrics $engagementMetrics,
        public readonly BehavioralMetrics $behavioralMetrics,
        public readonly CategoryBreakdown $categoryBreakdown,
        public readonly FeedbackSamples $feedbackSamples,
        public readonly array $gamificationBadges, // GamificationBadge[]
        public readonly TechnicalDebtAnalysis $technicalDebtAnalysis,
        public readonly ManagementDecisionAssist $managementDecisionAssist,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            metaData: MetaData::fromArray($data['meta_data'] ?? []),
            executiveSummary: ExecutiveSummary::fromArray($data['executive_summary'] ?? []),
            classification: Classification::fromArray($data['classification'] ?? []),
            authorAnalytics: AuthorAnalytics::fromArray($data['author_analytics'] ?? []),
            qualityMetrics: QualityMetrics::fromArray($data['quality_metrics'] ?? []),
            velocityMetrics: VelocityMetrics::fromArray($data['velocity_metrics'] ?? []),
            trendAnalysis: TrendAnalysis::fromArray($data['trend_analysis'] ?? []),
            educationalRecommendation: EducationalRecommendation::fromArray($data['educational_recommendation'] ?? []),
            reviewersAnalytics: array_map(
                fn($reviewer) => ReviewerAnalytics::fromArray($reviewer),
                $data['reviewers_analytics'] ?? []
            ),
            engagementMetrics: EngagementMetrics::fromArray($data['engagement_metrics'] ?? []),
            behavioralMetrics: BehavioralMetrics::fromArray($data['behavioral_metrics'] ?? []),
            categoryBreakdown: CategoryBreakdown::fromArray($data['category_breakdown'] ?? []),
            feedbackSamples: FeedbackSamples::fromArray($data['feedback_samples'] ?? []),
            gamificationBadges: array_map(
                fn($badge) => GamificationBadge::fromArray($badge),
                $data['gamification_badges'] ?? []
            ),
            technicalDebtAnalysis: TechnicalDebtAnalysis::fromArray($data['technical_debt_analysis'] ?? []),
            managementDecisionAssist: ManagementDecisionAssist::fromArray($data['management_decision_assist'] ?? []),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'meta_data' => $this->metaData,
            'executive_summary' => $this->executiveSummary,
            'classification' => $this->classification,
            'author_analytics' => $this->authorAnalytics,
            'quality_metrics' => $this->qualityMetrics,
            'velocity_metrics' => $this->velocityMetrics,
            'trend_analysis' => $this->trendAnalysis,
            'educational_recommendation' => $this->educationalRecommendation,
            'reviewers_analytics' => $this->reviewersAnalytics,
            'engagement_metrics' => $this->engagementMetrics,
            'behavioral_metrics' => $this->behavioralMetrics,
            'category_breakdown' => $this->categoryBreakdown,
            'feedback_samples' => $this->feedbackSamples,
            'gamification_badges' => $this->gamificationBadges,
            'technical_debt_analysis' => $this->technicalDebtAnalysis,
            'management_decision_assist' => $this->managementDecisionAssist,
        ];
    }

    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    public function getOverallRiskColor(): string
    {
        return $this->classification->getRiskColor();
    }

    public function getQualityColor(): string
    {
        return $this->qualityMetrics->getQualityColor();
    }

    public function hasGamificationBadges(): bool
    {
        return !empty($this->gamificationBadges);
    }

    public function getTotalScore(): float
    {
        return $this->qualityMetrics->score;
    }
}