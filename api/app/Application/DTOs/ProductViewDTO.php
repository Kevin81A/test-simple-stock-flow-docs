<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class ProductViewDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly float $price,
        public readonly string $currency,
        public readonly int $stock,
        public readonly string $categoryId,
        public readonly string $categoryName,
        public readonly ?string $imageUrl = null
    ) {}
}
