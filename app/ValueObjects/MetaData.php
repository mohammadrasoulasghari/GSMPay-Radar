<?php

namespace App\ValueObjects;

use JsonSerializable;

/**
 * Meta data about the analysis.
 */
class MetaData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $analysisTimestamp = null,
        public readonly ?string $modelVersion = null,
    ) {}

    public static function fromArray(?array $data): self
    {
        if (empty($data)) {
            return new self();
        }

        return new self(
            analysisTimestamp: $data['analysis_timestamp'] ?? null,
            modelVersion: $data['model_version'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return array_filter([
            'analysis_timestamp' => $this->analysisTimestamp,
            'model_version' => $this->modelVersion,
        ], fn($v) => $v !== null);
    }
}
