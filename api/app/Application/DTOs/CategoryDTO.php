<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class CategoryDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name
    ) {}
}
