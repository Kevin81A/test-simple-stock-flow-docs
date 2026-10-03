<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Exceptions\ProductNotFoundException;

final class SoftDeleteProductUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly FileStorageInterface $fileStorage
    ) {}

    public function execute(string $id): void
    {
        $product = $this->productRepository->findById($id);
        if ($product === null) {
            throw new ProductNotFoundException($id);
        }

        $imageKey = $product->imageKey();

        // 1. Mark as soft-deleted in repository first (DB update)
        $this->productRepository->softDelete($id);

        // 2. If it had an image, delete the physical binary after database update confirms
        if ($imageKey !== null) {
            $this->fileStorage->delete($imageKey);
        }
    }
}
