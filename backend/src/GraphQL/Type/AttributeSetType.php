<?php

declare(strict_types=1);

namespace App\GraphQL\Type;

use App\Domain\Attribute\AttributeSet;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeSetType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeSet',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => fn (AttributeSet $s): int => $s->getId(),
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (AttributeSet $s): string => $s->getName(),
                ],
                'type' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => fn (AttributeSet $s): string => $s->getType(),
                ],
                'items' => [
                    'type' => Type::nonNull(Type::listOf(TypeRegistry::attributeItem())),
                    'resolve' => fn (AttributeSet $s): array => $s->getItems(),
                ],
            ],
        ]);
    }
}
