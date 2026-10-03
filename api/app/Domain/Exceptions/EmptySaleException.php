<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class EmptySaleException extends DomainException
{
    public function __construct(string $message = 'Sale must contain at least one product line.')
    {
        parent::__construct($message, 400);
    }
}