<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidDateRangeException extends DomainException
{
    public function __construct(string $message = "Invalid date range: 'from' must be earlier than or equal to 'to'.")
    {
        parent::__construct($message, 400);
    }
}