<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Repository\MySQLCategoryRepository;
use App\Repository\MySQLProductRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Query',
            'fields' => function () {
                return [
                    '_ping' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => static fn (): string => 'ok',
                    ],
                    'categories' => [
                        'type' => Type::nonNull(Type::listOf(TypeRegistry::category())),
                        'resolve' => function ($root, $args, $context) {
                            $repo = new MySQLCategoryRepository($context['pdo']);
                            return $repo->findAll();
                        },
                    ],
                    'products' => [
                        'type' => Type::nonNull(Type::listOf(TypeRegistry::product())),
                        'args' => [
                            'category' => ['type' => Type::string()],
                        ],
                        'resolve' => function ($root, $args, $context) {
                            $repo = new MySQLProductRepository($context['pdo']);
                            $categoryName = $args['category'] ?? null;
                            return $repo->findByCategoryName($categoryName);
                        },
                    ],
                    'product' => [
                        'type' => TypeRegistry::product(),
                        'args' => [
                            'id' => Type::nonNull(Type::id()),
                        ],
                        'resolve' => function ($root, $args, array $context) {
                            $repo = new MySQLProductRepository($context['pdo']);
                            return $repo->findById($args['id']);
                        },
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}
