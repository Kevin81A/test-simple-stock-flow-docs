<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repositories;

use App\Application\Ports\Outbound\UnitOfWorkInterface;
use Illuminate\Support\Facades\DB;

final class DatabaseUnitOfWork implements UnitOfWorkInterface
{
    public function transaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }

    public function discardChanges(): void
    {
        // Resets or clears query log/internal states if any
    }
}
