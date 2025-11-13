<?php

declare(strict_types=1);

namespace App\Domain\Attribute;

class SwatchAttributeSet extends AttributeSet
{
    public function getType(): string
    {
        return 'swatch';
    }
}
