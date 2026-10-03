<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidPriceException extends DomainException
{
    public function __construct(string $message = 'Product price must be greater than zero.')
    {
        parent::__construct($message, 400);
    }
}