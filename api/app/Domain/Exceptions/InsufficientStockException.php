<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InsufficientStockException extends DomainException
{
    public function __construct(string $productName, int $requested, int $available)
    {
        parent::__construct(
            "Insufficient stock for product '{$productName}'. Requested: {$requested}, Available: {$available}.",
            422
        );
    }
}