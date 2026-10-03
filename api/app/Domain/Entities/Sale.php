<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\Exceptions\DuplicateSaleProductException;
use App\Domain\Exceptions\EmptySaleException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use DateTimeImmutable;

final class Sale
{
    private string $id;
    private DateTimeImmutable $soldAt;
    private string $soldByUserId;
    private string $soldByUsername;
    /** @var array<string, SaleItem> */
    private array $items = [];

    public function __construct(
        string $id,
        DateTimeImmutable $soldAt,
        string $soldByUserId,
        string $soldByUsername,
        array $items = []
    ) {
        $this->id = $id;
        $this->soldAt = $soldAt;
        $this->soldByUserId = $soldByUserId;
        $this->soldByUsername = $soldByUsername;

        foreach ($items as $item) {
            if ($item instanceof SaleItem) {
                $this->items[$item->productId()] = $item;
            }
        }
    }

    public static function open(
        string $id,
        DateTimeImmutable $soldAt,
        string $soldByUserId,
        string $soldByUsername
    ): self {
        return new self($id, $soldAt, $soldByUserId, $soldByUsername);
    }

    public function id(): string
    {
        return $this->id;
    }

    public function soldAt(): DateTimeImmutable
    {
        return $this->soldAt;
    }

    public function soldByUserId(): string
    {
        return $this->soldByUserId;
    }

    public function soldByUsername(): string
    {
        return $this->soldByUsername;
    }

    /**
     * @return list<SaleItem>
     */
    public function items(): array
    {
        return array_values($this->items);
    }

    public function addItem(Product $product, Quantity $quantity, string $categoryName): void
    {
        $productId = $product->id();
        if (isset($this->items[$productId])) {
            throw new DuplicateSaleProductException('La venta tiene productos repetidos.');
        }

        $product->withdraw($quantity);

        $this->items[$productId] = new SaleItem(
            $productId,
            $product->name(),
            $product->price(),
            $categoryName,
            $quantity
        );
    }

    public function ensureConfirmable(): void
    {
        if (empty($this->items)) {
            throw new EmptySaleException('La venta debe tener al menos un ítem.');
        }
    }

    public function total(): Money
    {
        $total = Money::zero();
        foreach ($this->items as $item) {
            $total = $total->plus($item->subtotal());
        }
        return $total;
    }
}
