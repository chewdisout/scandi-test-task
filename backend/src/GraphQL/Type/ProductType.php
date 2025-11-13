<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Product\Product;
use App\Repository\MySQLAttributeRepository;
use App\Repository\MySQLGalleryRepository;
use App\Repository\MySQLPriceRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'fields' => function () {
                return [
                    'id' => [
                        'type' => Type::nonNull(Type::id()),
                        'resolve' => fn (Product $p): string => $p->getId(),
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'resolve' => fn (Product $p): string => $p->getName(),
                    ],
                    'inStock' => [
                        'type' => Type::nonNull(Type::boolean()),
                        'resolve' => fn (Product $p): bool => $p->isInStock(),
                    ],
                    'description' => [
                        'type' => Type::string(),
                        'resolve' => fn (Product $p): string => $p->getDescription(),
                    ],
                    'brand' => [
                        'type' => Type::string(),
                        'resolve' => fn (Product $p): string => $p->getBrand(),
                    ],
                    'gallery' => [
                        'type' => Type::nonNull(Type::listOf(Type::nonNull(Type::string()))),
                        'resolve' => function (Product $p, $args, $context): array {
                            $repo = new MySQLGalleryRepository($context['pdo']);
                            return $repo->findByProductId($p->getId());
                        },
                    ],
                    'prices' => [
                        'type' => Type::nonNull(Type::listOf(TypeRegistry::price())),
                        'resolve' => function (Product $p, $args, $context): array {
                            $repo = new MySQLPriceRepository($context['pdo']);
                            return $repo->findByProductId($p->getId());
                        },
                    ],
                    'attributes' => [
                        'type' => Type::nonNull(Type::listOf(TypeRegistry::attributeSet())),
                        'resolve' => function (Product $p, $args, $context): array {
                            $repo = new MySQLAttributeRepository($context['pdo']);
                            return $repo->findByProductId($p->getId());
                        },
                    ],
                ];
            },
        ]);
    }
}
