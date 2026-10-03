<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\DomainException;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Exceptions\InvalidPriceException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use InvalidArgumentException;

final class Product
{
    private string $id;
    private string $name;
    private Money $price;
    private int $stock;
    private string $categoryId;
    private ?string $imageKey;

    public function __construct(
        string $id,
        string $name,
        Money $price,
        int $stock,
        string $categoryId,
        ?string $imageKey = null
    ) {
        $this->id = $id;
        $this->rename($name);
        $this->changePrice($price);

        if ($stock < 0) {
            throw new class('El stock inicial no puede ser negativo.') extends DomainException {};
        }
        $this->stock = $stock;

        $this->setCategory($categoryId);
        $this->attachImage($imageKey);
    }

    public static function create(
        string $id,
        string $name,
        Money $price,
        int $stock,
        string $categoryId,
        ?string $imageKey = null
    ): self {
        return new self($id, $name, $price, $stock, $categoryId, $imageKey);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function imageKey(): ?string
    {
        return $this->imageKey;
    }

    public function rename(string $name): void
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            throw new class('El nombre del producto es obligatorio.') extends DomainException {};
        }
        $this->name = $trimmed;
    }

    public function changePrice(Money $price): void
    {
        if ($price->currency() !== Money::DEFAULT_CURRENCY) {
            throw new InvalidArgumentException(sprintf("Moneda no válida: '%s'. Solo se admite COP.", $price->currency()));
        }

        if (!$price->isPositive()) {
            throw new InvalidPriceException('El precio debe ser mayor a cero.');
        }

        $this->price = $price;
    }

    public function setCategory(string $categoryId): void
    {
        $trimmed = trim($categoryId);
        if ($trimmed === '' || $trimmed === '00000000-0000-0000-0000-000000000000') {
            throw new class('La categoría es obligatoria.') extends DomainException {};
        }
        $this->categoryId = $trimmed;
    }

    public function attachImage(?string $imageKey): void
    {
        $this->imageKey = ($imageKey !== null && trim($imageKey) !== '') ? trim($imageKey) : null;
    }

    public function withdraw(Quantity $quantity): void
    {
        $requested = $quantity->value();
        if ($this->stock < $requested) {
            throw new InsufficientStockException($this->name, $this->stock, $requested);
        }

        $this->stock -= $requested;
    }

    public function restock(Quantity $quantity): void
    {
        $this->stock += $quantity->value();
    }
}
