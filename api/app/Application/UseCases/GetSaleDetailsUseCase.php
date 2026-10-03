<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\SaleItemViewDTO;
use App\Application\DTOs\SaleViewDTO;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Domain\Exceptions\DomainException;

final class GetSaleDetailsUseCase
{
    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository
    ) {}

    public function execute(string $id): SaleViewDTO
    {
        $sale = $this->saleRepository->findById($id);
        if ($sale === null) {
            throw new class('La venta no existe.') extends DomainException {
                public function __construct(string $message) {
                    parent::__construct($message, 404);
                }
            };
        }

        $items = [];
        foreach ($sale->items() as $item) {
            $items[] = new SaleItemViewDTO(
                $item->productId(),
                $item->productName(),
                $item->quantity()->value(),
                $item->unitPrice()->amount(),
                $item->subtotal()->amount()
            );
        }

        return new SaleViewDTO(
            $sale->id(),
            $sale->soldAt()->format('Y-m-d\TH:i:s.u\+00:00'),
            $sale->soldByUsername(),
            $sale->total()->amount(),
            $sale->total()->currency(),
            $items
        );
    }
}
