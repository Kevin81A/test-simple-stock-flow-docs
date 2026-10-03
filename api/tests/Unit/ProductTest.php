<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entities\Product;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Exceptions\InvalidPriceException;
use App\Domain\Exceptions\InvalidQuantityException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function test_can_create_valid_product(): void
    {
        $product = new Product(
            id: 1,
            name: 'Specialty Coffee',
            price: Money::fromFloat(12.50),
            stock: new Quantity(50),
            categoryId: 1,
            version: 1
        );

        $this->assertSame(1, $product->getId());
        $this->assertSame('Specialty Coffee', $product->getName());
        $this->assertSame(12.50, $product->getPrice()->toFloat());
        $this->assertSame(50, $product->getStock()->toInt());
        $this->assertSame(1, $product->getVersion());
    }

    public function test_deduct_stock_success(): void
    {
        $product = new Product(
            id: 1,
            name: 'Specialty Coffee',
            price: Money::fromFloat(10.00),
            stock: new Quantity(10),
            categoryId: 1,
            version: 1
        );

        $product->deductStock(new Quantity(4));
        $this->assertSame(6, $product->getStock()->toInt());
    }

    public function test_deduct_stock_throws_exception_when_insufficient(): void
    {
        $this->expectException(InsufficientStockException::class);

        $product = new Product(
            id: 1,
            name: 'Specialty Coffee',
            price: Money::fromFloat(10.00),
            stock: new Quantity(2),
            categoryId: 1,
            version: 1
        );

        $product->deductStock(new Quantity(5));
    }

    public function test_negative_price_throws_exception(): void
    {
        $this->expectException(InvalidPriceException::class);
        Money::fromFloat(-5.00);
    }

    public function test_negative_quantity_throws_exception(): void
    {
        $this->expectException(InvalidQuantityException::class);
        new Quantity(-1);
    }
}
