<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class SaleItemViewDTO
{
    public function __construct(
        public readonly string $productId,
        public readonly string $productName,
        public readonly int $quantity,
        public readonly float $unitPrice,
        public readonly float $subtotal
    ) {}
}
