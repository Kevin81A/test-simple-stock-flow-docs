<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidQuantityException;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidQuantityException('La cantidad debe ser mayor a cero.');
        }

        $this->value = $value;
    }

    public static function of(int $value): self
    {
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}
