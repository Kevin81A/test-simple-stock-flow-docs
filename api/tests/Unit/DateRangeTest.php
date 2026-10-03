<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exceptions\InvalidDateRangeException;
use App\Domain\ValueObjects\DateRange;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class DateRangeTest extends TestCase
{
    public function test_valid_date_range(): void
    {
        $start = new DateTimeImmutable('2026-10-01T00:00:00Z');
        $end = new DateTimeImmutable('2026-10-03T23:59:59Z');

        $range = new DateRange($start, $end);

        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());
    }

    public function test_start_after_end_throws_exception(): void
    {
        $this->expectException(InvalidDateRangeException::class);

        $start = new DateTimeImmutable('2026-10-05T00:00:00Z');
        $end = new DateTimeImmutable('2026-10-01T00:00:00Z');

        new DateRange($start, $end);
    }
}
