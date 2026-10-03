<?php

declare(strict_types=1);

namespace App\Application\DTOs;

/**
 * @template T
 */
final class PagedResultDTO
{
    /**
     * @param list<T> $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $page,
        public readonly int $size,
        public readonly int $total,
        public readonly int $totalPages
    ) {}

    /**
     * @param list<T> $items
     */
    public static function create(array $items, int $page, int $size, int $total): self
    {
        $totalPages = $size <= 0 ? 0 : (int) ceil($total / $size);
        return new self($items, $page, $size, $total, $totalPages);
    }
}
