<?php

declare(strict_types=1);

namespace App\Domain\Order;

use App\Domain\Product\Product;

class OrderItem
{
    /**
     * @param SelectedAttribute[] $selectedAttributes
     */
    public function __construct(
        private int $id,
        private Product $product,
        private int $quantity,
        private array $selectedAttributes
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return SelectedAttribute[]
     */
    public function getSelectedAttributes(): array
    {
        return $this->selectedAttributes;
    }
}
