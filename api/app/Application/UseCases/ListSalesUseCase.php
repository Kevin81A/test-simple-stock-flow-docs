<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\PagedResultDTO;
use App\Application\DTOs\SaleItemViewDTO;
use App\Application\DTOs\SaleViewDTO;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\ValueObjects\DateRange;

final class ListSalesUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    /**
     * @return PagedResultDTO<SaleViewDTO>
     */
    public function execute(DateRange $range, int $page, int $size): PagedResultDTO
    {
        $servedPage = $page < 1 ? 1 : $page;
        $servedSize = $size < 1 ? 20 : ($size > 100 ? 100 : $size);

        $result = $this->saleRepository->searchByDateRangePaginated($range, $servedPage, $servedSize);

        $items = [];
        foreach ($result['items'] as $sale) {
            $lineItems = [];
            foreach ($sale->items() as $item) {
                $lineItems[] = new SaleItemViewDTO(
                    $item->productId(),
                    $item->productName(),
                    $item->quantity()->value(),
                    $item->unitPrice()->amount(),
                    $item->subtotal()->amount()
                );
            }

            $items[] = new SaleViewDTO(
                $sale->id(),
                $sale->soldAt()->format('Y-m-d\TH:i:s.u\+00:00'),
                $sale->soldByUsername(),
                $sale->total()->amount(),
                $sale->total()->currency(),
                $lineItems
            );
        }

        return PagedResultDTO::create($items, $servedPage, $servedSize, $result['total']);
    }
}
