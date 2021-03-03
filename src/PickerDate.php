<?php

declare(strict_types=1);

namespace DanBettles\DatePicker;

use DateTimeImmutable;

class PickerDate
{
    private DateTimeImmutable $dateTime;

    private array $events;

    public function __construct(DateTimeImmutable $dateTime)
    {
        $this
            ->setDateTime($dateTime)
            ->setEvents()
        ;
    }

    private function setDateTime(DateTimeImmutable $dateTime): self
    {
        $this->dateTime = $dateTime;
        return $this;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function addEvent(string $event): self
    {
        $this->events[] = $event;
        return $this;
    }

    public function setEvents(array $events = []): self
    {
        $this->events = [];

        foreach ($events as $event) {
            $this->addEvent($event);
        }

        return $this;
    }

    private function getEvents(): array
    {
        return $this->events;
    }

    public function getNumEvents(): int
    {
        return \count($this->getEvents());
    }

    public function hasEvents(): bool
    {
        return $this->getNumEvents() > 0;
    }
}
