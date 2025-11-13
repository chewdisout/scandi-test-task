<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Order\OrderItem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderItem',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn (OrderItem $i): int => $i->getId(),
                ],
                'product' => [
                    'type' => TypeRegistry::product(),
                    'resolve' => fn (OrderItem $i) => $i->getProduct(),
                ],
                'quantity' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => fn (OrderItem $i): int => $i->getQuantity(),
                ],
                'selectedAttributes' => [
                    'type' => Type::nonNull(Type::listOf(TypeRegistry::selectedAttribute())),
                    'resolve' => fn (OrderItem $i): array => $i->getSelectedAttributes(),
                ],
            ],
        ]);
    }
}
