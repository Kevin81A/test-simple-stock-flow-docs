<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entities\Sale;
use App\Domain\ValueObjects\DateRange;

interface SaleRepositoryInterface
{
    public function findById(string $id): ?Sale;

    /**
     * @return array{items: list<Sale>, total: int}
     */
    public function searchByDateRangePaginated(DateRange $range, int $page, int $size): array;

    public function save(Sale $sale): void;
}
