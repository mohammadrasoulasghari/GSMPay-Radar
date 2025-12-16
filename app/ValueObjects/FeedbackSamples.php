<?php

namespace App\ValueObjects;

use JsonSerializable;

class FeedbackSamples implements JsonSerializable
{
    public function __construct(
        public readonly array $positive,
        public readonly array $constructive,
        public readonly array $negative,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            positive: $data['positive'] ?? [],
            constructive: $data['constructive'] ?? [],
            negative: $data['negative'] ?? [],
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'positive' => $this->positive,
            'constructive' => $this->constructive,
            'negative' => $this->negative,
        ];
    }

    public function getAllFeedback(): array
    {
        return array_merge($this->positive, $this->constructive, $this->negative);
    }

    public function getRandomPositive(): ?string
    {
        return $this->positive[array_rand($this->positive)] ?? null;
    }

    public function getRandomConstructive(): ?string
    {
        return $this->constructive[array_rand($this->constructive)] ?? null;
    }
}