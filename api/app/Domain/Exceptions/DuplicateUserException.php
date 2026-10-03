<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class DuplicateUserException extends DomainException
{
    public function __construct(string $username)
    {
        parent::__construct("User '{$username}' is already registered.", 400);
    }
}