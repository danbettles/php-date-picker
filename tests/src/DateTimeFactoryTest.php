<?php

declare(strict_types=1);

namespace DanBettles\DatePicker\Tests;

use DanBettles\DatePicker\DateTimeFactory;
use DateTime;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DateTimeFactoryTest extends TestCase
{
    public function testCreateimmutable()
    {
        $factory = new DateTimeFactory();

        $source1 = new DateTimeImmutable('2020-03-14 20:50:00');
        $created1 = $factory->createImmutable($source1);

        $this->assertInstanceOf(DateTimeImmutable::class, $created1);
        $this->assertEquals($source1, $created1);
        $this->assertNotSame($source1, $created1);

        $source2 = new DateTime('1978-08-26 18:00:00');
        $created2 = $factory->createImmutable($source2);

        $this->assertInstanceOf(DateTimeImmutable::class, $created2);
        $this->assertEquals($source2, $created2);

        $source3 = '1983-09-10 12:00:00';
        $created3 = $factory->createImmutable($source3);

        $this->assertInstanceOf(DateTimeImmutable::class, $created3);
        $this->assertSame($source3, $created3->format('Y-m-d H:i:s'));
    }

    public function providesInvalidTypesOfDates(): array
    {
        return [
            [null],
            [123],
        ];
    }

    /**
     * @dataProvider providesInvalidTypesOfDates
     */
    public function testCreateimmutableThrowsAnExceptionIfTheTypeOfTheInputIsInvalid($invalidDate)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The type of the input is invalid.');

        (new DateTimeFactory())->createImmutable($invalidDate);
    }
}
