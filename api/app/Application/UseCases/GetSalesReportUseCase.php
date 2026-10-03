<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\SalesReportDTO;
use App\Application\DTOs\SalesReportRowDTO;
use App\Application\Ports\Outbound\SalesReportQueryInterface;
use App\Domain\ValueObjects\DateRange;

final class GetSalesReportUseCase
{
    public function __construct(
        private readonly SalesReportQueryInterface $salesReportQuery
    ) {}

    public function execute(DateRange $range): SalesReportDTO
    {
        $data = $this->salesReportQuery->getReport($range);

        $rows = [];
        foreach ($data['rows'] as $r) {
            $rows[] = new SalesReportRowDTO(
                $r['productId'],
                $r['productName'],
                $r['categoryName'],
                $r['unitsSold'],
                $r['revenue']
            );
        }

        return new SalesReportDTO(
            $range->from()->format('Y-m-d\TH:i:s.u\+00:00'),
            $range->to()->format('Y-m-d\TH:i:s.u\+00:00'),
            $data['salesCount'],
            $data['grandTotal'],
            $data['currency'] ?? 'COP',
            $rows
        );
    }
}
