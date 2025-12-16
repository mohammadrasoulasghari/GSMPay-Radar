<?php

namespace App\ValueObjects;

use JsonSerializable;

class CategoryBreakdown implements JsonSerializable
{
    public function __construct(
        public readonly int $codeStyle,
        public readonly int $architectureDesign,
        public readonly int $security,
        public readonly int $productRequirement,
        public readonly int $other,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            codeStyle: (int) ($data['code_style'] ?? 0),
            architectureDesign: (int) ($data['architecture_design'] ?? 0),
            security: (int) ($data['security'] ?? 0),
            productRequirement: (int) ($data['product_requirement'] ?? 0),
            other: (int) ($data['other'] ?? 0),
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'code_style' => $this->codeStyle,
            'architecture_design' => $this->architectureDesign,
            'security' => $this->security,
            'product_requirement' => $this->productRequirement,
            'other' => $this->other,
        ];
    }

    public function getTotal(): int
    {
        return $this->codeStyle + $this->architectureDesign + $this->security + $this->productRequirement + $this->other;
    }

    public function toChartData(): array
    {
        return [
            'Code Style' => $this->codeStyle,
            'Architecture' => $this->architectureDesign,
            'Security' => $this->security,
            'Product' => $this->productRequirement,
            'Other' => $this->other,
        ];
    }
}