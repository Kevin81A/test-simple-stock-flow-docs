<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

use App\Domain\Entities\Product;

interface ProductRepositoryInterface
{
    public function findById(string $id): ?Product;

    public function findActiveById(string $id): ?Product;

    /**
     * @return array{items: list<Product>, total: int}
     */
    public function searchPaginated(?string $search, ?string $categoryId, int $page, int $size): array;

    public function save(Product $product): void;

    public function updateWithOptimisticLock(Product $product, int $expectedVersion): void;

    public function softDelete(string $id): void;
}
