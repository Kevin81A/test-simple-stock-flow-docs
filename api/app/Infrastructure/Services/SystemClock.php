<?php

declare(strict_types=1);

namespace App\Infrastructure\Services;

use App\Application\Ports\Outbound\ClockInterface;
use DateTimeImmutable;
use DateTimeZone;

final class SystemClock implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
