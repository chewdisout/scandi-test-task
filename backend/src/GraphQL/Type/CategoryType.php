<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Category\Category;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => function (Category $category): int {
                        return $category->getId();
                    },
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => function (Category $category): string {
                        return $category->getName();
                    },
                ],
            ],
        ]);
    }
}
