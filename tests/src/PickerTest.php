<?php

declare(strict_types=1);

namespace DanBettles\DatePicker\Tests;

use DanBettles\DatePicker\Picker;
use DanBettles\DatePicker\PickerDate;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PickerTest extends TestCase
{
    public function testIsInstantiable()
    {
        $pickerUsingDateFromRequest = new Picker(['on' => '2021-03-14']);

        $this->assertEquals([
            'defaultDateTimeStr' => 'today',
            'requestVarName' => 'on',
            'requestVarFormat' => 'Y-m-d',
            'requestVarPattern' => '/^\d{4}-\d{2}-\d{2}$/',
            'titleDateFormat' => 'F Y',
            'firstDayOfWeek' => '1',
        ], $pickerUsingDateFromRequest->getOptions());

        $this->assertEquals(new DateTimeImmutable('2021-03-14'), $pickerUsingDateFromRequest->getSelectedDatetime());

        $pickerUsingDefaultDate = new Picker([]);

        $this->assertEquals(new DateTimeImmutable('today'), $pickerUsingDefaultDate->getSelectedDatetime());
    }

    public function testGetoptionsCanReturnASpecificElement()
    {
        $picker = new Picker([]);

        $this->assertSame('on', $picker->getOptions('requestVarName'));
    }

    public function providesTitles(): array
    {
        return [
            [
                (new DateTimeImmutable())->format('F Y'),
                new Picker([]),
            ],
            [
                'March 2021',
                new Picker(['on' => '2021-03-14']),
            ],
        ];
    }

    /**
     * @dataProvider providesTitles
     */
    public function testCreatetitle($expectedTitle, Picker $picker)
    {
        $this->assertSame($expectedTitle, $picker->createTitle());
    }

    public function providesPrevMonthUrls(): array
    {
        return [
            [
                '?on=2021-02-01',
                new Picker(['on' => '2021-03-14']),
            ],
            [
                '?on=2020-12-01',
                new Picker(['on' => '2021-01-21']),
            ],
        ];
    }

    /**
     * @dataProvider providesPrevMonthUrls
     */
    public function testCreateprevmonthurl($expectedUrl, Picker $picker)
    {
        $this->assertSame($expectedUrl, $picker->createPrevMonthUrl());
    }

    public function providesNextMonthUrls(): array
    {
        return [
            [
                '?on=2021-04-01',
                new Picker(['on' => '2021-03-14']),
            ],
            [
                '?on=2021-01-01',
                new Picker(['on' => '2020-12-14']),
            ],
        ];
    }

    /**
     * @dataProvider providesNextMonthUrls
     */
    public function testCreatenextmonthurl($expectedUrl, Picker $picker)
    {
        $this->assertSame($expectedUrl, $picker->createNextMonthUrl());
    }

    public function testCreatecolumnheaders()
    {
        $picker = new Picker([]);

        $this->assertEquals([
            'M',
            'T',
            'W',
            'T',
            'F',
            'S',
            'S',
        ], $picker->createColumnHeaders());
    }

    public function testCreaterowdata()
    {
        $picker = new Picker(['on' => '2021-01-30']);

        $this->assertEquals([
            [
                null,
                null,
                null,
                null,
                $this->createPickerDate('2021-01-01'),
                $this->createPickerDate('2021-01-02'),
                $this->createPickerDate('2021-01-03'),
            ],
            [
                $this->createPickerDate('2021-01-04'),
                $this->createPickerDate('2021-01-05'),
                $this->createPickerDate('2021-01-06'),
                $this->createPickerDate('2021-01-07'),
                $this->createPickerDate('2021-01-08'),
                $this->createPickerDate('2021-01-09'),
                $this->createPickerDate('2021-01-10'),
            ],
            [
                $this->createPickerDate('2021-01-11'),
                $this->createPickerDate('2021-01-12'),
                $this->createPickerDate('2021-01-13'),
                $this->createPickerDate('2021-01-14'),
                $this->createPickerDate('2021-01-15'),
                $this->createPickerDate('2021-01-16'),
                $this->createPickerDate('2021-01-17'),
            ],
            [
                $this->createPickerDate('2021-01-18'),
                $this->createPickerDate('2021-01-19'),
                $this->createPickerDate('2021-01-20'),
                $this->createPickerDate('2021-01-21'),
                $this->createPickerDate('2021-01-22'),
                $this->createPickerDate('2021-01-23'),
                $this->createPickerDate('2021-01-24'),
            ],
            [
                $this->createPickerDate('2021-01-25'),
                $this->createPickerDate('2021-01-26'),
                $this->createPickerDate('2021-01-27'),
                $this->createPickerDate('2021-01-28'),
                $this->createPickerDate('2021-01-29'),
                $this->createPickerDate('2021-01-30'),
                $this->createPickerDate('2021-01-31'),
            ],
        ], $picker->createRowData());
    }

    public function testCreatedateurl()
    {
        $picker = new Picker([]);

        $this->assertSame('?on=1983-09-10', $picker->createDateUrl(new DateTimeImmutable('1983-09-10')));
    }

    public function testAddevents()
    {
        $picker = new Picker(['on' => '2021-01-30']);

        $picker->addEvents([
            '2021-01-01' => [
                'Foo',
                'Bar',
            ],
            '2021-01-28' => [
                'Baz',
            ],
        ]);

        $this->assertEquals([
            [
                null,
                null,
                null,
                null,
                $this->createPickerDateWithEvents('2021-01-01', [
                    'Foo',
                    'Bar',
                ]),
                $this->createPickerDate('2021-01-02'),
                $this->createPickerDate('2021-01-03'),
            ],
            [
                $this->createPickerDate('2021-01-04'),
                $this->createPickerDate('2021-01-05'),
                $this->createPickerDate('2021-01-06'),
                $this->createPickerDate('2021-01-07'),
                $this->createPickerDate('2021-01-08'),
                $this->createPickerDate('2021-01-09'),
                $this->createPickerDate('2021-01-10'),
            ],
            [
                $this->createPickerDate('2021-01-11'),
                $this->createPickerDate('2021-01-12'),
                $this->createPickerDate('2021-01-13'),
                $this->createPickerDate('2021-01-14'),
                $this->createPickerDate('2021-01-15'),
                $this->createPickerDate('2021-01-16'),
                $this->createPickerDate('2021-01-17'),
            ],
            [
                $this->createPickerDate('2021-01-18'),
                $this->createPickerDate('2021-01-19'),
                $this->createPickerDate('2021-01-20'),
                $this->createPickerDate('2021-01-21'),
                $this->createPickerDate('2021-01-22'),
                $this->createPickerDate('2021-01-23'),
                $this->createPickerDate('2021-01-24'),
            ],
            [
                $this->createPickerDate('2021-01-25'),
                $this->createPickerDate('2021-01-26'),
                $this->createPickerDate('2021-01-27'),
                $this->createPickerDateWithEvents('2021-01-28', [
                    'Baz',
                ]),
                $this->createPickerDate('2021-01-29'),
                $this->createPickerDate('2021-01-30'),
                $this->createPickerDate('2021-01-31'),
            ],
        ], $picker->createRowData());
    }

    public function providesInvalidDateStrings(): array
    {
        return [
            ['30-01-2021'],
        ];
    }

    /**
     * @dataProvider providesInvalidDateStrings
     */
    public function testGetselecteddatestrThrowsAnExceptionIfTheSelectedDateIsInvalid(string $invalidDateStr)
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The format of the selected date is invalid.');

        new Picker(['on' => $invalidDateStr]);
    }

    //###> Factory methods ###

    private function createPickerDate(string $dateStr): PickerDate
    {
        return new PickerDate(new DateTimeImmutable($dateStr));
    }

    private function createPickerDateWithEvents(string $dateStr, array $events): PickerDate
    {
        $pickerDate = $this->createPickerDate($dateStr);

        foreach ($events as $event) {
            $pickerDate->addEvent($event);
        }

        return $pickerDate;
    }

    //###< Factory methods ###
}
