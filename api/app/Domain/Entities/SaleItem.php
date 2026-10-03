<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;

final class SaleItem
{
    private string $productId;
    private string $productName;
    private Money $unitPrice;
    private string $categoryName;
    private Quantity $quantity;

    public function __construct(
        string $productId,
        string $productName,
        Money $unitPrice,
        string $categoryName,
        Quantity $quantity
    ) {
        $this->productId = $productId;
        $this->productName = $productName;
        $this->unitPrice = $unitPrice;
        $this->categoryName = $categoryName;
        $this->quantity = $quantity;
    }

    public function productId(): string
    {
        return $this->productId;
    }

    public function productName(): string
    {
        return $this->productName;
    }

    public function unitPrice(): Money
    {
        return $this->unitPrice;
    }

    public function categoryName(): string
    {
        return $this->categoryName;
    }

    public function quantity(): Quantity
    {
        return $this->quantity;
    }

    public function subtotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity->value());
    }
}
