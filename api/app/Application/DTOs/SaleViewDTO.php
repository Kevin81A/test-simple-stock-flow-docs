<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class SaleViewDTO
{
    /**
     * @param list<SaleItemViewDTO> $items
     */
    public function __construct(
        public readonly string $id,
        public readonly string $soldAt,
        public readonly string $soldBy,
        public readonly float $total,
        public readonly string $currency,
        public readonly array $items
    ) {}
}
