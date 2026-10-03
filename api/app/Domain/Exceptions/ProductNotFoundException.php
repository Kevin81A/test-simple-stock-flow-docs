<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class ProductNotFoundException extends DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct("Product not found: {$productId}.", 404);
    }
}