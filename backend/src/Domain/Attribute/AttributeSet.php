<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

abstract class AttributeSet
{
    /** @param AttributeItem[] $items */
    public function __construct(
        protected int $id,
        protected string $name,
        protected array $items
    ) {}

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }

    /** @return AttributeItem[] */
    public function getItems(): array { return $this->items; }

    abstract public function getType(): string;
}
