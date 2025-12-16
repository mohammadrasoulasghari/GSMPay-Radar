<?php

namespace App\ValueObjects;

use JsonSerializable;
use Throwable;

/**
 * Main AI Analysis Value Object.
 * This represents the complete analysis response from the AI.
 * 
 * Required fields in schema:
 * - executive_summary
 * - classification
 * - author_analytics
 * - reviewers_analytics
 * - gamification_badges
 * - management_decision_assist
 */
class AiAnalysis implements JsonSerializable
{
    public function __construct(
        public readonly ExecutiveSummary $executiveSummary,
        public readonly Classification $classification,
        public readonly AuthorAnalytics $authorAnalytics,
        public readonly array $reviewersAnalytics, // ReviewerAnalytics[]
        public readonly array $gamificationBadges, // GamificationBadge[]
        public readonly ManagementDecisionAssist $managementDecisionAssist,
        public readonly ?MetaData $metaData = null,
        public readonly ?TechnicalDebtAnalysis $technicalDebtAnalysis = null,
    ) {}

    /**
     * Create AiAnalysis from array with full error handling.
     * Returns null if data is empty or invalid.
     */
    public static function fromArray(?array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        try {
            // Parse reviewers analytics safely
            $reviewersAnalytics = [];
            if (!empty($data['reviewers_analytics']) && is_array($data['reviewers_analytics'])) {
                foreach ($data['reviewers_analytics'] as $reviewer) {
                    if (is_array($reviewer)) {
                        $reviewersAnalytics[] = ReviewerAnalytics::fromArray($reviewer);
                    }
                }
            }

            // Parse gamification badges safely
            $gamificationBadges = [];
            if (!empty($data['gamification_badges']) && is_array($data['gamification_badges'])) {
                foreach ($data['gamification_badges'] as $badge) {
                    if (is_array($badge)) {
                        $gamificationBadges[] = GamificationBadge::fromArray($badge);
                    }
                }
            }

            return new self(
                executiveSummary: ExecutiveSummary::fromArray($data['executive_summary'] ?? null),
                classification: Classification::fromArray($data['classification'] ?? null),
                authorAnalytics: AuthorAnalytics::fromArray($data['author_analytics'] ?? null),
                reviewersAnalytics: $reviewersAnalytics,
                gamificationBadges: $gamificationBadges,
                managementDecisionAssist: ManagementDecisionAssist::fromArray($data['management_decision_assist'] ?? null),
                metaData: isset($data['meta_data']) ? MetaData::fromArray($data['meta_data']) : null,
                technicalDebtAnalysis: isset($data['technical_debt_analysis']) 
                    ? TechnicalDebtAnalysis::fromArray($data['technical_debt_analysis']) 
                    : null,
            );
        } catch (Throwable $e) {
            // Log error if needed, return null for safety
            report($e);
            return null;
        }
    }

    public function jsonSerialize(): array
    {
        $result = [
            'executive_summary' => $this->executiveSummary,
            'classification' => $this->classification,
            'author_analytics' => $this->authorAnalytics,
            'reviewers_analytics' => $this->reviewersAnalytics,
            'gamification_badges' => $this->gamificationBadges,
            'management_decision_assist' => $this->managementDecisionAssist,
        ];

        if ($this->metaData !== null) {
            $result['meta_data'] = $this->metaData;
        }

        if ($this->technicalDebtAnalysis !== null) {
            $result['technical_debt_analysis'] = $this->technicalDebtAnalysis;
        }

        return $result;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this->jsonSerialize()), true);
    }

    // === Helper methods ===

    public function getHealthStatus(): string
    {
        return $this->executiveSummary->overallHealthStatus;
    }

    public function getHealthColor(): string
    {
        return $this->executiveSummary->getHealthColor();
    }

    public function getRiskLevel(): string
    {
        return $this->classification->riskLevel;
    }

    public function getRiskColor(): string
    {
        return $this->classification->getRiskColor();
    }

    public function getChangeType(): string
    {
        return $this->classification->changeType;
    }

    public function isBlocking(): bool
    {
        return $this->classification->isBlocking;
    }

    public function getBusinessValueClarity(): int
    {
        return $this->executiveSummary->businessValueClarity;
    }

    public function getSolidCompliance(): int
    {
        return $this->authorAnalytics->qualityMetrics->solidCompliance;
    }

    public function getAuthorIdentity(): string
    {
        return $this->authorAnalytics->identity;
    }

    public function getEducationalPath(): array
    {
        return $this->authorAnalytics->educationalPath;
    }

    public function getRecurringMistakes(): array
    {
        return $this->authorAnalytics->trendAnalysis->recurringMistakes;
    }

    public function getReviewers(): array
    {
        return $this->reviewersAnalytics;
    }

    public function getReviewersCount(): int
    {
        return count($this->reviewersAnalytics);
    }

    public function hasGamificationBadges(): bool
    {
        return !empty($this->gamificationBadges);
    }

    public function getBadgesCount(): int
    {
        return count($this->gamificationBadges);
    }

    public function hasTechnicalDebt(): bool
    {
        return $this->technicalDebtAnalysis !== null 
            && $this->technicalDebtAnalysis->addedDebtLevel !== 'none';
    }

    public function isOverEngineered(): bool
    {
        return $this->technicalDebtAnalysis?->overEngineeringDetected ?? false;
    }

    public function getRefactorSuggestions(): array
    {
        return $this->technicalDebtAnalysis?->suggestionsForRefactor ?? [];
    }

    public function requiresHrAttention(): bool
    {
        return $this->managementDecisionAssist->hrFlag;
    }

    public function getFinalVerdict(): string
    {
        return $this->managementDecisionAssist->finalVerdictFa;
    }

    /**
     * Calculate average tone score from all reviewers.
     */
    public function getAverageToneScore(): float
    {
        if (empty($this->reviewersAnalytics)) {
            return 0.0;
        }

        $scores = array_map(
            fn(ReviewerAnalytics $r) => $r->behavioralMetrics->toneScore,
            $this->reviewersAnalytics
        );

        return round(array_sum($scores) / count($scores), 2);
    }
}
