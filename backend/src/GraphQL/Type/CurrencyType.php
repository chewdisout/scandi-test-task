<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Price\Currency;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CurrencyType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Currency',
            'fields' => [
                'label' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (Currency $c): string => $c->getLabel(),
                ],
                'symbol' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (Currency $c): string => $c->getSymbol(),
                ],
            ],
        ]);
    }
}
