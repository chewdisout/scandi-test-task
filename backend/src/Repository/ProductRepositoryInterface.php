<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Product\Product;

interface ProductRepositoryInterface
{
    /** @return Product[] */
    public function findByCategoryName(?string $categoryName): array;
}
