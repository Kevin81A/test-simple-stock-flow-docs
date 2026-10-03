<?php

declare(strict_types=1);

namespace App\Domain\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    public function __construct(string $message = '', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
