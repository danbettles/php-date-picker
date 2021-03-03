<?php

use DanBettles\DatePicker\DateTimeDecorator;
use DanBettles\DatePicker\Picker;
use DanBettles\DatePicker\PickerDate;

/** @var Picker $this */
?>
<div class="date-picker">
    <table>
        <caption>
            <div>
                <div class="title"><?= $this->createTitle() ?></div>

                <div class="month-pagination">
                    <a href="<?= $this->createPrevMonthUrl() ?>" class="previous"><span>Previous month</span></a>
                    <a href="<?= $this->createNextMonthUrl() ?>" class="next"><span>Next month</span></a>
                </div>
            </div>
        </caption>

        <thead>
            <tr>
                <?php foreach ($this->createColumnHeaders() as $columnHeader) : ?>
                    <th scope="col">
                        <span><?= $columnHeader ?></span>
                    </th>
                <?php endforeach ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($this->createRowData() as $pickerDates) : ?>
                <tr>
                    <?php /** @var PickerDate $pickerDate */ ?>
                    <?php foreach ($pickerDates as $pickerDate) : ?>
                        <?php if (null === $pickerDate) : ?>
                            <td class="empty"></td>
                        <?php else : ?>
                            <?php $decorator = new DateTimeDecorator($pickerDate->getDateTime()) ?>

                            <?php $titleSentences = \array_filter([
                                $decorator->isToday() ? 'Today' : '',
                                $pickerDate->hasEvents() ? "{$pickerDate->getNumEvents()} events on this date" : '',
                            ]) ?>

                            <?php $cssClasses = \array_filter([
                                $decorator->isToday() ? 'today' : '',
                                $decorator->hasSameDateAs($this->getSelectedDatetime()) ? 'selected' : '',
                                $pickerDate->hasEvents() ? 'has-events' : '',
                            ]) ?>

                            <td title="<?= \implode('. ', $titleSentences) ?>" class="<?= \implode(' ', $cssClasses) ?>">
                                <a href="<?= $this->createDateUrl($pickerDate->getDateTime()) ?>">
                                    <?= $pickerDate->getDateTime()->format('j') ?>
                                </a>
                            </td>
                        <?php endif ?>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
