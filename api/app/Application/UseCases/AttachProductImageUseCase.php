<?php

declare(strict_types=1);

namespace App\Application\UseCases;

use App\Application\Ports\Outbound\FileStorageInterface;
use App\Application\Ports\Outbound\ProductRepositoryInterface;
use App\Domain\Exceptions\ProductNotFoundException;

final class AttachProductImageUseCase
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly FileStorageInterface $fileStorage
    ) {}

    public function execute(string $productId, string $tempPath, string $extension): string
    {
        $product = $this->productRepository->findById($productId);
        if ($product === null) {
            throw new ProductNotFoundException($productId);
        }

        // Store new image binary
        $key = $this->fileStorage->store($tempPath, $extension);

        // Delete previous image if existed
        $oldKey = $product->imageKey();
        if ($oldKey !== null && $oldKey !== $key) {
            $this->fileStorage->delete($oldKey);
        }

        $product->attachImage($key);
        $this->productRepository->save($product);

        return $this->fileStorage->getUrl($key);
    }
}
