<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidQuantityException extends DomainException
{
    public function __construct(string $message = 'Quantity must be greater than zero.')
    {
        parent::__construct($message, 400);
    }
}