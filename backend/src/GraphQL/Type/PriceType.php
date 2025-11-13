<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Price\Price;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Price',
            'fields' => [
                'amount' => [
                    'type' => Type::nonNull(Type::float()),
                    'resolve' => fn (Price $p): float => $p->getAmount(),
                ],
                'currency' => [
                    'type' => Type::nonNull(TypeRegistry::currency()),
                    'resolve' => fn (Price $p) => $p->getCurrency(),
                ],
            ],
        ]);
    }
}
