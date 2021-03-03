<?php

declare(strict_types=1);

namespace DanBettles\DatePicker\Tests;

use DanBettles\DatePicker\DateTimeDecorator;
use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

class DateTimeDecoratorTest extends TestCase
{
    public function testIsInstantiable()
    {
        $dateTime = new DateTimeImmutable();
        $decorator = new DateTimeDecorator($dateTime);

        $this->assertSame($dateTime, $decorator->getDateTime());
    }

    public function providesTodayAndOtherDates(): array
    {
        return [
            [true, new DateTimeDecorator(new DateTimeImmutable())],
            [true, $this->createDecorator('now')],
            [false, $this->createDecorator('yesterday')],
            [false, $this->createDecorator('tomorrow')],
        ];
    }

    /**
     * @dataProvider providesTodayAndOtherDates
     */
    public function testIstodayReturnsTrueIfTheDecoratorRepresentsToday($isToday, DateTimeDecorator $decorator)
    {
        $this->assertSame($isToday, $decorator->isToday());
    }

    public function providesWeekends(): array
    {
        return [
            [true, $this->createDecorator('next saturday')],
            [true, $this->createDecorator('next sunday')],
            [false, $this->createDecorator('next monday')],
            [false, $this->createDecorator('next tuesday')],
            [false, $this->createDecorator('next wednesday')],
            [false, $this->createDecorator('next thursday')],
            [false, $this->createDecorator('next friday')],
        ];
    }

    /**
     * @dataProvider providesWeekends
     */
    public function testIsweekend($isWeekend, DateTimeDecorator $decorator)
    {
        $this->assertSame($isWeekend, $decorator->isWeekend());
    }

    public function providesDatetimesOnTheSameDate(): array
    {
        return [
            [true, $this->createDecorator('today'), new DateTimeImmutable('today')],
            [true, $this->createDecorator('today 0100'), new DateTimeImmutable('today 2300')],
            [false, $this->createDecorator('today'), new DateTimeImmutable('tomorrow 0100')],
        ];
    }

    /**
     * @dataProvider providesDatetimesOnTheSameDate
     */
    public function testHassamedateas($hasSameDate, DateTimeDecorator $decorator, DateTimeInterface $dateTime)
    {
        $this->assertSame($hasSameDate, $decorator->hasSameDateAs($dateTime));
    }

    //###> Factory methods ###

    private function createDecorator(string $dateStr): DateTimeDecorator
    {
        return new DateTimeDecorator(new DateTimeImmutable($dateStr));
    }

    //###< Factory methods ###
}
