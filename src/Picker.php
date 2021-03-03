<?php

declare(strict_types=1);

namespace DanBettles\DatePicker;

use DateTimeImmutable;
use InvalidArgumentException;

class Picker
{
    /** @var string[] */
    private const DAY_NAMES = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    /** @var int */
    private const DAYS_PER_WEEK = 7;

    /** @var string */
    private const FORMAT_MYSQL_DATE = 'Y-m-d';

    private array $options;

    private array $requestVars;

    private array $pickerDates;

    public function __construct(array $requestVars)
    {
        $this
            ->setOptions([
                'defaultDateTimeStr' => 'today',
                'requestVarName' => 'on',
                'requestVarFormat' => self::FORMAT_MYSQL_DATE,
                'requestVarPattern' => '/^\d{4}-\d{2}-\d{2}$/',
                'titleDateFormat' => 'F Y',
                'firstDayOfWeek' => 1,  //ISO-8601 numeric representation of the day of the week.
            ])
            ->setRequestVars($requestVars)
            ->calculatePickerDates()
        ;
    }

    //###> Accessors ###

    private function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOptions(?string $key = null)
    {
        if (null !== $key) {
            return $this->options[$key];
        }

        return $this->options;
    }

    /**
     * @throws InvalidArgumentException If the format of the selected date is invalid.
     */
    private function setRequestVars(array $requestVars): self
    {
        $requestVarName = $this->getOptions('requestVarName');

        if (\array_key_exists($requestVarName, $requestVars)) {
            $selectedDateStr = $requestVars[$requestVarName];

            if (!\preg_match($this->getOptions('requestVarPattern'), $selectedDateStr)) {
                throw new InvalidArgumentException('The format of the selected date is invalid.');
            }
        }

        $this->requestVars = $requestVars;

        return $this;
    }

    private function getRequestVars(): array
    {
        return $this->requestVars;
    }

    private function setPickerDates(array $pickerDates): self
    {
        $this->pickerDates = $pickerDates;
        return $this;
    }

    /**
     * @return PickerDate[]
     */
    private function getPickerDates(): array
    {
        return $this->pickerDates;
    }

    //###< Accessors ###

    /**
     * Returns a datetime representing: the date selected by the user; or, if the user didn't select a date, the default
     * date.
     *
     * @return DateTimeImmutable
     */
    public function getSelectedDatetime(): DateTimeImmutable
    {
        $requestVarName = $this->getOptions('requestVarName');

        $selectedDateStr = \array_key_exists($requestVarName, $this->getRequestVars())
            ? $this->getRequestVars()[$requestVarName]
            : $this->getOptions('defaultDateTimeStr')
        ;

        return new DateTimeImmutable($selectedDateStr);
    }

    private function createFirstDatetimeInMonth(): DateTimeImmutable
    {
        return $this
            ->getSelectedDatetime()
            ->modify('first day of')
        ;
    }

    private function calculatePickerDates(): self
    {
        $numDaysInMonth = (int) $this->getSelectedDatetime()->format('t');
        $firstDatetimeInMonth = $this->createFirstDatetimeInMonth();

        $pickerDates = [];

        for ($i = 0; $i < $numDaysInMonth; $i += 1) {
            $loopDatetime = $firstDatetimeInMonth->modify(\sprintf('+%d days', $i));
            $pickerDates[$loopDatetime->format(self::FORMAT_MYSQL_DATE)] = new PickerDate($loopDatetime);
        }

        return $this->setPickerDates($pickerDates);
    }

    public function addEvents(array $events): self
    {
        foreach ($this->getPickerDates() as $dateStr => $pickerDate) {
            if (!\array_key_exists($dateStr, $events)) {
                continue;
            }

            $pickerDate->setEvents($events[$dateStr]);
        }

        return $this;
    }

    public function createTitle(): string
    {
        return $this
            ->getSelectedDatetime()
            ->format($this->getOptions('titleDateFormat'))
        ;
    }

    public function createDateUrl(DateTimeImmutable $dateTime): string
    {
        return \sprintf(
            '?%s=%s',
            $this->getOptions('requestVarName'),
            $dateTime->format($this->getOptions('requestVarFormat'))
        );
    }

    public function createPrevMonthUrl(): string
    {
        $destDateTime = $this
            ->getSelectedDatetime()
            ->modify('first day of previous month')
        ;

        return $this->createDateUrl($destDateTime);
    }

    public function createNextMonthUrl(): string
    {
        $destDateTime = $this
            ->getSelectedDatetime()
            ->modify('first day of next month')
        ;

        return $this->createDateUrl($destDateTime);
    }

    /**
     * @return string[]
     */
    public function createColumnHeaders(): array
    {
        $firstDayOfWeek0 = $this->getOptions('firstDayOfWeek') - 1;
        $firstDayNames = \array_slice(self::DAY_NAMES, $firstDayOfWeek0);
        $subsequentDayNames = \array_slice(self::DAY_NAMES, 0, $firstDayOfWeek0);
        $reorderedDayNames = \array_merge($firstDayNames, $subsequentDayNames);

        return \array_map(function (string $dayName): string {
            return $dayName[0];
        }, $reorderedDayNames);
    }

    public function createRowData(): array
    {
        $firstDatetimeInMonth = $this->createFirstDatetimeInMonth();
        $dayNoOfFirstDate = (int) $firstDatetimeInMonth->format('N');
        $firstDayOfWeekInPicker = $this->getOptions('firstDayOfWeek');

        $numLeadingEmptyDays = $dayNoOfFirstDate >= $firstDayOfWeekInPicker
            ? $dayNoOfFirstDate - $firstDayOfWeekInPicker
            : (self::DAYS_PER_WEEK - $firstDayOfWeekInPicker) + $dayNoOfFirstDate
        ;

        //Pad the start of the calendar if necessary.
        $dayData = \array_fill(0, $numLeadingEmptyDays, null);

        $dayData = \array_merge($dayData, $this->getPickerDates());

        //Pad the end of the calendar if necessary.
        while (0 !== \count($dayData) % self::DAYS_PER_WEEK) {
            $dayData[] = null;
        }

        //Split the array into week-long chunks.
        return \array_chunk($dayData, self::DAYS_PER_WEEK);
    }

    public function render(): string
    {
        \ob_start();
        require __DIR__ . '/../templates/picker.html.php';
        $output = \ob_get_clean();

        return $output;
    }
}
