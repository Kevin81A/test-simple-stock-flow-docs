<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\CreateProductDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Entities\Product;
use App\Domain\Exceptions\CategoryNotFoundException;
use App\Domain\ValueObjects\Money;

final class CreateProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function execute(CreateProductDTO $dto): string
    {
        if (!$this->categoryRepository->existsById($dto->categoryId)) {
            throw new CategoryNotFoundException($dto->categoryId);
        }

        $id = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        $price = Money::of($dto->price);

        $product = Product::create(
            $id,
            $dto->name,
            $price,
            $dto->stock,
            $dto->categoryId
        );

        $this->productRepository->save($product);

        return $id;
    }
}
