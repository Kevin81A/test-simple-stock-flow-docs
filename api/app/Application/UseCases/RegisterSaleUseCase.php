<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\RegisterSaleDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ClockInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Application\Ports\Outbound\SaleRepositoryInterface;
use App\Application\Ports\Outbound\UnitOfWorkInterface;
use App\Domain\Entities\Sale;
use App\Domain\Exceptions\ConcurrencyConflictException;
use App\Domain\Exceptions\DuplicateSaleProductException;
use App\Domain\Exceptions\EmptySaleException;
use App\Domain\Exceptions\InvalidQuantityException;
use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\ValueObjects\Quantity;

final class RegisterSaleUseCase
{
    private const MAX_RETRIES = 3;

    public function __construct(
        private readonly SaleRepositoryInterface $saleRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly UnitOfWorkInterface $unitOfWork,
        private readonly ClockInterface $clock
    ) {}

    public function execute(RegisterSaleDTO $dto): string
    {
        // 1. Check lines is not empty
        if (empty($dto->lines)) {
            throw new EmptySaleException('La venta debe tener al menos un ítem.');
        }

        // 2. Check for duplicate product IDs in request
        $seen = [];
        foreach ($dto->lines as $line) {
            $productId = $line['productId'] ?? '';
            if (isset($seen[$productId])) {
                throw new DuplicateSaleProductException('La venta tiene productos repetidos.');
            }
            $seen[$productId] = true;
        }

        $attempts = 0;
        while ($attempts < self::MAX_RETRIES) {
            $attempts++;
            $this->unitOfWork->discardChanges();

            try {
                return $this->unitOfWork->transaction(function () use ($dto): string {
                    $saleId = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
                    $sale = Sale::open(
                        $saleId,
                        $this->clock->now(),
                        $dto->soldByUserId,
                        $dto->soldByUsername
                    );

                    // Preload categories
                    $categories = [];
                    foreach ($this->categoryRepository->listAll() as $c) {
                        $categories[$c->id()] = $c->name();
                    }

                    foreach ($dto->lines as $line) {
                        $productId = (string) ($line['productId'] ?? '');
                        $quantityVal = (int) ($line['quantity'] ?? 0);

                        // 3. Product existence (must be active, not deleted)
                        $product = $this->productRepository->findActiveById($productId);
                        if ($product === null) {
                            throw new ProductNotFoundException($productId);
                        }

                        // 4. Quantity must be > 0
                        if ($quantityVal <= 0) {
                            throw new InvalidQuantityException('La cantidad debe ser mayor a cero.');
                        }
                        $quantity = new Quantity($quantityVal);

                        // 5. Category name frozen
                        $catName = $categories[$product->categoryId()] ?? 'General';

                        // 6. Withdraw stock and add item
                        $sale->addItem($product, $quantity, $catName);
                        $this->productRepository->save($product);
                    }

                    $sale->ensureConfirmable();
                    $this->saleRepository->save($sale);

                    return $saleId;
                });
            } catch (ConcurrencyConflictException $e) {
                if ($attempts >= self::MAX_RETRIES) {
                    throw $e;
                }
            }
        }

        throw new ConcurrencyConflictException();
    }
}
