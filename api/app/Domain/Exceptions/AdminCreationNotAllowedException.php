<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class AdminCreationNotAllowedException extends DomainException
{
    public function __construct(
        string $message = 'Only sellers can be registered. The administrator is created during deployment.'
    ) {
        parent::__construct($message);
    }
}