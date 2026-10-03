<?php

declare(strict_types=1);

namespace App\Application\DTOs;

final class RegisterSaleDTO
{
    /**
     * @param list<array{productId: string, quantity: int}> $lines
     */
    public function __construct(
        public readonly array $lines,
        public readonly string $soldByUserId,
        public readonly string $soldByUsername
    ) {}
}
