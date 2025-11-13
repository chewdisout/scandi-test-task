<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Repository\MySQLOrderRepository;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType extends ObjectType
{
    public function __construct()
    {
        $selectedAttributeInput = new InputObjectType([
            'name' => 'SelectedAttributeInput',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
            ],
        ]);

        $orderItemInput = new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'productId' => Type::nonNull(Type::id()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => Type::listOf(
                    Type::nonNull($selectedAttributeInput)
                ),
            ],
        ]);

        $createOrderInput = new InputObjectType([
            'name' => 'CreateOrderInput',
            'fields' => [
                'items' => Type::nonNull(
                    Type::listOf(Type::nonNull($orderItemInput))
                ),
            ],
        ]);

        parent::__construct([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => TypeRegistry::order(),
                    'args' => [
                        'input' => Type::nonNull($createOrderInput),
                    ],
                    'resolve' => function ($root, array $args, array $context) {
                        $repo = new MySQLOrderRepository($context['pdo']);
                        return $repo->createOrder($args['input']);
                    },
                ],
            ],
        ]);
    }
}
