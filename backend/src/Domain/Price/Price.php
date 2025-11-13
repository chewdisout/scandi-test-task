<?php

declare(strict_types=1);

namespace App\Domain\Price;

class Price
{
    public function __construct(
        private Currency $currency,
        private float $amount
    ) {}

    public function getCurrency(): Currency { return $this->currency; }
    public function getAmount(): float { return $this->amount; }
}
