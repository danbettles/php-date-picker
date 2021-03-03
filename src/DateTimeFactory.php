<?php

declare(strict_types=1);

namespace DanBettles\DatePicker;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

class DateTimeFactory
{
    /**
     * Creates an immutable from any type of date/time.
     *
     * @param DateTimeInterface|string $something
     * @throws InvalidArgumentException If the type of the input is invalid.
     */
    public function createImmutable($something): DateTimeImmutable
    {
        if ($something instanceof DateTimeImmutable) {
            return clone $something;
        }

        if ($something instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($something);
        }

        if (\is_string($something)) {
            return new DateTimeImmutable($something);
        }

        throw new InvalidArgumentException('The type of the input is invalid.');
    }
}
