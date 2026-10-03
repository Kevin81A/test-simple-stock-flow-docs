<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class SalesReportDTO
{
    /**
     * @param list<SalesReportRowDTO> $rows
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly int $salesCount,
        public readonly float $grandTotal,
        public readonly string $currency,
        public readonly array $rows
    ) {}
}
