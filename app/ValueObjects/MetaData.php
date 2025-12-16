<?php

namespace App\ValueObjects;

use JsonSerializable;

class MetaData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $analysisTimestamp = null,
        public readonly ?string $modelVersion = null,
        public readonly float $confidenceLevel = 0.0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            analysisTimestamp: $data['analysis_timestamp'] ?? null,
            modelVersion: $data['model_version'] ?? $data['ai_model_version'] ?? null,
            confidenceLevel: (float) ($data['confidence_level'] ?? 0.0),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'analysis_timestamp' => $this->analysisTimestamp,
            'model_version' => $this->modelVersion,
            'confidence_level' => $this->confidenceLevel,
        ];
    }
}