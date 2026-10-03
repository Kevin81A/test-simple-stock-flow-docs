<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\DTOs\ProductViewDTO;
use App\Application\Ports\Outbound\CategoryRepositoryInterface;
use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Exceptions\ProductNotFoundException;

final class GetProductByIdUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly FileStorageInterface $fileStorage
    ) {}

    public function execute(string $id): ProductViewDTO
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        $category = $this->categoryRepository->findById($product->categoryId());
        $categoryName = $category !== null ? $category->name() : 'General';
        $imageUrl = $product->imageKey() !== null ? $this->fileStorage->getUrl($product->imageKey()) : null;

        return new ProductViewDTO(
            $product->id(),
            $product->name(),
            $product->price()->amount(),
            $product->price()->currency(),
            $product->stock(),
            $product->categoryId(),
            $categoryName,
            $imageUrl
        );
    }
}
