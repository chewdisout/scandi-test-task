<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

class TextAttributeSet extends AttributeSet
{
    public function getType(): string
    {
        return 'text';
    }
}
