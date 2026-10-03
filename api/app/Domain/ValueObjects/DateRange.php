<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use App\Domain\Exceptions\InvalidDateRangeException;
use DateTimeImmutable;

final class DateRange
{
    private DateTimeImmutable $from;
    private DateTimeImmutable $to;

    public function __construct(DateTimeImmutable $from, DateTimeImmutable $to)
    {
        if ($to < $from) {
            throw new InvalidDateRangeException('La fecha final no puede ser anterior a la inicial.');
        }

        $this->from = $from;
        $this->to = $to;
    }

    public static function of(DateTimeImmutable $from, DateTimeImmutable $to): self
    {
        return new self($from, $to);
    }

    public function from(): DateTimeImmutable
    {
        return $this->from;
    }

    public function to(): DateTimeImmutable
    {
        return $this->to;
    }

    public function contains(DateTimeImmutable $date): bool
    {
        return $date >= $this->from && $date < $this->to;
    }
}
