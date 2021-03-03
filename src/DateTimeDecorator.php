<?php

declare(strict_types=1);

namespace DanBettles\DatePicker;

use DateTimeImmutable;
use DateTimeInterface;

class DateTimeDecorator
{
    private DateTimeInterface $dateTime;

    public function __construct(DateTimeInterface $dateTime)
    {
        $this->setDateTime($dateTime);
    }

    private function setDateTime(DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->dateTime;
    }

    /**
     * Returns `true` if the specified datetime has the same date as the decorator's datetime, or `false` otherwise.
     */
    public function hasSameDateAs(DateTimeInterface $otherDateTime): bool
    {
        $dateTimeFactory = new DateTimeFactory();

        $thisImmutable = $dateTimeFactory
            ->createImmutable($this->getDateTime())
            ->setTime(0, 0, 0, 0)
        ;

        $thatImmutable = $dateTimeFactory
            ->createImmutable($otherDateTime)
            ->setTime(0, 0, 0, 0)
        ;

        return $thisImmutable == $thatImmutable;
    }

    public function isToday(): bool
    {
        return $this->hasSameDateAs(new DateTimeImmutable());
    }

    public function isWeekend(): bool
    {
        $dayNo = (int) $this->getDateTime()->format('N');

        return 6 === $dayNo || 7 === $dayNo;
    }
}
