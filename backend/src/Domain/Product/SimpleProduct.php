<?php

declare(strict_types=1);

namespace App\Domain\Product;

class SimpleProduct extends Product
{
    public function getType(): string
    {
        return 'simple';
    }
}
