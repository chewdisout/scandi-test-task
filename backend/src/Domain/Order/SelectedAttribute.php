<?php

declare(strict_types=1);

namespace App\Domain\Order;

class SelectedAttribute
{
    public function __construct(
        private string $name,
        private string $value
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data['name'], $data['value']);
    }
}
