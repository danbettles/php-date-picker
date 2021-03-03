<?php

declare(strict_types=1);

namespace DanBettles\DatePicker\Tests;

use DanBettles\DatePicker\PickerDate;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PickerDateTest extends TestCase
{
    public function testIsInstantiable()
    {
        $dateTime = new DateTimeImmutable();
        $pickerDate = new PickerDate($dateTime);

        $this->assertSame($dateTime, $pickerDate->getDateTime());
    }

    public function testAddeventAddsAnEvent()
    {
        $pickerDate = new PickerDate(new DateTimeImmutable());

        $this->assertFalse($pickerDate->hasEvents());
        $this->assertSame(0, $pickerDate->getNumEvents());

        $something = $pickerDate->addEvent('Foo');

        $this->assertSame($pickerDate, $something);

        $this->assertTrue($pickerDate->hasEvents());
        $this->assertSame(1, $pickerDate->getNumEvents());

        $pickerDate->addEvent('Bar');

        $this->assertTrue($pickerDate->hasEvents());
        $this->assertSame(2, $pickerDate->getNumEvents());
    }

    public function testSetevents()
    {
        $pickerDate = new PickerDate(new DateTimeImmutable());

        $something = $pickerDate->setEvents([
            'Foo',
            'Bar',
        ]);

        $this->assertSame($pickerDate, $something);

        $this->assertTrue($pickerDate->hasEvents());
        $this->assertSame(2, $pickerDate->getNumEvents());

        $pickerDate->setEvents([]);

        $this->assertFalse($pickerDate->hasEvents());
        $this->assertSame(0, $pickerDate->getNumEvents());
    }

    public function testSeteventsClearsTheEventsByDefault()
    {
        $pickerDate = (new PickerDate(new DateTimeImmutable()))
            ->setEvents([
                'Foo',
                'Bar',
            ])
            ->setEvents()
        ;

        $this->assertFalse($pickerDate->hasEvents());
        $this->assertSame(0, $pickerDate->getNumEvents());
    }
}
