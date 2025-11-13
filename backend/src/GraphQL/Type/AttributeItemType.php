<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Attribute\AttributeItem;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn (AttributeItem $i): int => $i->getId(),
                ],
                'displayValue' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (AttributeItem $i): string => $i->getDisplayValue(),
                ],
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (AttributeItem $i): string => $i->getValue(),
                ],
            ],
        ]);
    }
}
