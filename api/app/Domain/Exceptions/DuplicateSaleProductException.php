<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class DuplicateSaleProductException extends DomainException
{
    public function __construct(string $productId)
    {
        parent::__construct("Sale cannot contain duplicate product: {$productId}.", 400);
    }
}