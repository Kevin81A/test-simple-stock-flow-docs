<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class SalesReportRowDTO
{
    public function __construct(
        public readonly string $productId,
        public readonly string $productName,
        public readonly string $categoryName,
        public readonly int $unitsSold,
        public readonly float $revenue
    ) {}
}
