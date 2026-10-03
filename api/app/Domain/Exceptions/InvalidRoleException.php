<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class InvalidRoleException extends DomainException
{
    public function __construct(string $role)
    {
        parent::__construct("Invalid role '{$role}'. Allowed roles are 'admin' and 'seller'.", 400);
    }
}