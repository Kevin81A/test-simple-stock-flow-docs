<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidPriceException;
use InvalidArgumentException;

final class Money
{
    public const DEFAULT_CURRENCY = 'COP';

    private float $amount;
    private string $currency;

    public function __construct(float|int $amount, string $currency = self::DEFAULT_CURRENCY)
    {
        if ($currency !== self::DEFAULT_CURRENCY) {
            throw new InvalidArgumentException(sprintf("Moneda no soportada: %s. Solo se admite COP.", $currency));
        }

        if ($amount < 0) {
            throw new InvalidPriceException("El precio no puede ser negativo.");
        }

        $this->amount = round((float) $amount, 2, PHP_ROUND_HALF_UP);
        $this->currency = self::DEFAULT_CURRENCY;
    }

    public static function of(float|int $amount, string $currency = self::DEFAULT_CURRENCY): self
    {
        return new self($amount, $currency);
    }

    public static function zero(): self
    {
        return new self(0.0);
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function plus(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("No se pueden operar importes en distintas monedas.");
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function multiply(int|float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function equals(self $other): bool
    {
        return $this->currency === $other->currency && abs($this->amount - $other->amount) < 0.0001;
    }
}
