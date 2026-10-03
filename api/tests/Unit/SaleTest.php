<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entities\Sale;
use App\Domain\Entities\SaleItem;
use App\Domain\Exceptions\DuplicateSaleProductException;
use App\Domain\Exceptions\EmptySaleException;
use App\Domain\ValueObjects\Money;
use App\Domain\ValueObjects\Quantity;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class SaleTest extends TestCase
{
    public function test_can_create_valid_sale_with_calculated_total(): void
    {
        $item1 = new SaleItem(
            id: 1,
            saleId: 1,
            productId: 10,
            quantity: new Quantity(2),
            unitPrice: Money::fromFloat(15.00),
            subtotal: Money::fromFloat(30.00)
        );

        $item2 = new SaleItem(
            id: 2,
            saleId: 1,
            productId: 20,
            quantity: new Quantity(1),
            unitPrice: Money::fromFloat(25.50),
            subtotal: Money::fromFloat(25.50)
        );

        $sale = new Sale(
            id: 1,
            sellerId: 5,
            total: Money::fromFloat(55.50),
            createdAt: new DateTimeImmutable('2026-10-03T12:00:00Z'),
            items: [$item1, $item2]
        );

        $this->assertSame(1, $sale->getId());
        $this->assertSame(5, $sale->getSellerId());
        $this->assertSame(55.50, $sale->getTotal()->toFloat());
        $this->assertCount(2, $sale->getItems());
    }

    public function test_empty_sale_items_throws_exception(): void
    {
        $this->expectException(EmptySaleException::class);

        new Sale(
            id: 1,
            sellerId: 5,
            total: Money::fromFloat(0.00),
            createdAt: new DateTimeImmutable(),
            items: []
        );
    }

    public function test_duplicate_product_in_sale_throws_exception(): void
    {
        $this->expectException(DuplicateSaleProductException::class);

        $item1 = new SaleItem(1, 1, 10, new Quantity(1), Money::fromFloat(10.0), Money::fromFloat(10.0));
        $item2 = new SaleItem(2, 1, 10, new Quantity(2), Money::fromFloat(10.0), Money::fromFloat(20.0));

        new Sale(
            id: 1,
            sellerId: 5,
            total: Money::fromFloat(30.0),
            createdAt: new DateTimeImmutable(),
            items: [$item1, $item2]
        );
    }
}
