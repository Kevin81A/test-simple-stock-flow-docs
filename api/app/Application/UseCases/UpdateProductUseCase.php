<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\UpdateProductDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entities\Product;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\Exceptions\ProductNotFoundException;
use App\Domain\ValueObjects\Money;

final class UpdateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(UpdateProductDTO $dto): void
    {
        $product = $this->productRepository->findById($dto->id);
        if ($product === null) {
            throw new ProductNotFoundException($dto->id);
        }

        if (!$this->categoryRepository->existsById($dto->categoryId)) {
            throw new CategoryNotFoundException($dto->categoryId);
        }

        $product->rename($dto->name);
        $product->changePrice(Money::of($dto->price));
        $product->setCategory($dto->categoryId);

        // Update stock: if different, adjust
        $diff = $dto->stock - $product->stock();
        if ($diff > 0) {
            $product->restock(new \App\Domain\ValueObjects\Quantity($diff));
        } elseif ($diff < 0) {
            $product->withdraw(new \App\Domain\ValueObjects\Quantity(abs($diff)));
        }

        $this->productRepository->save($product);
    }
}
