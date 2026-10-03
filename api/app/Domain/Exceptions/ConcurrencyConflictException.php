<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class ConcurrencyConflictException extends DomainException
{
    public function __construct(
        string $message = 'Concurrency conflict: product stock was modified by another operation. Please retry.'
    ) {
        parent::__construct($message, 409);
    }
}