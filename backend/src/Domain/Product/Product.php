<?php

declare(strict_types=1);

namespace App\Domain\Product;

abstract class Product
{
    public function __construct(
        protected string $id,
        protected string $name,
        protected bool $inStock,
        protected string $description,
        protected int $categoryId,
        protected string $brand
    ) {}

    abstract public function getType(): string;

    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function isInStock(): bool { return $this->inStock; }
    public function getDescription(): string { return $this->description; }
    public function getCategoryId(): int { return $this->categoryId; }
    public function getBrand(): string { return $this->brand; }
}
