<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Order\SelectedAttribute;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'SelectedAttribute',
            'fields' => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (SelectedAttribute $a): string => $a->getName(),
                ],
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (SelectedAttribute $a): string => $a->getValue(),
                ],
            ],
        ]);
    }
}
