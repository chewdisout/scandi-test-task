<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Order\Order;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Order',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn (Order $o): int => $o->getId(),
                ],
                'createdAt' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (Order $o): string => $o->getCreatedAt(),
                ],
                'items' => [
                    'type' => Type::nonNull(Type::listOf(TypeRegistry::orderItem())),
                    'resolve' => fn (Order $o): array => $o->getItems(),
                ],
            ],
        ]);
    }
}
