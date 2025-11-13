<?php

declare(strict_types=1);

namespace App\Domain\Order;

class Order
{
    /**
     * @param OrderItem[] $items
     */
    public function __construct(
        private int $id,
        private string $createdAt,
        private array $items
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @return OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
