<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Feedback samples for reviewer analytics.
 */
class ReviewerFeedbackSamples implements JsonSerializable
{
    public function __construct(
        public readonly ?string $bestCommentQuote = null,
        public readonly ?string $worstCommentQuote = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self();
        }

        return new self(
            bestCommentQuote: $data['best_comment_quote'] ?? null,
            worstCommentQuote: $data['worst_comment_quote'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'best_comment_quote' => $this->bestCommentQuote,
            'worst_comment_quote' => $this->worstCommentQuote,
        ], fn($v) => $v !== null);
    }

    public function hasBestComment(): bool
    {
        return !empty($this->bestCommentQuote);
    }

    public function hasWorstComment(): bool
    {
        return !empty($this->worstCommentQuote);
    }
}
