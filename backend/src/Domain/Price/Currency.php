<?php

declare(strict_types=1);

namespace App\Domain\Price;

class Currency
{
    public function __construct(
        private int $id,
        private string $label,
        private string $symbol
    ) {}

    public function getId(): int { return $this->id; }
    public function getLabel(): string { return $this->label; }
    public function getSymbol(): string { return $this->symbol; }
}
