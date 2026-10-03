<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

final class CategoryNotFoundException extends DomainException
{
    public function __construct(string $message = 'Category not found.')
    {
        parent::__construct($message, 404);
    }
}