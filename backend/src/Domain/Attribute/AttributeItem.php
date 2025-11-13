<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

class AttributeItem
{
    public function __construct(
        private int $id,
        private string $displayValue,
        private string $value,
        private string $slug
    ) {}

    public function getId(): int { return $this->id; }
    public function getDisplayValue(): string { return $this->displayValue; }
    public function getValue(): string { return $this->value; }
    public function getSlug(): string { return $this->slug; }
}
