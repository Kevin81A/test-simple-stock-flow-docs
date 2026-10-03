<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\ValueObjects\DateRange;

interface SalesReportQueryInterface
{
    /**
     * Aggregates sales directly in database with DP-01 window function/subquery.
     *
     * @return array{
     *     salesCount: int,
     *     grandTotal: float,
     *     currency: string,
     *     rows: list<array{
     *         productId: string,
     *         productName: string,
     *         categoryName: string,
     *         unitsSold: int,
     *         revenue: float
     *     }>
     * }
     */
    public function getReport(DateRange $range): array;
}
