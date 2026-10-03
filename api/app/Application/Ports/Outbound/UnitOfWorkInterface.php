<?php

declare(strict_types=1);

namespace App\Application\Ports\Outbound;

interface UnitOfWorkInterface
{
    /**
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function transaction(callable $callback): mixed;

    public function discardChanges(): void;
}
