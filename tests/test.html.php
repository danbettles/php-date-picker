<?php

use DanBettles\DatePicker\Picker;

require __DIR__ . '/../vendor/autoload.php';

//Ordinarily, you'd fetch events occurring in the selected month.
$events = [
    '2021-01-01' => [
        'Foo',
    ],
    '2021-02-14' => [
        'Bar',
    ],
    '2021-03-01' => [
        'Baz',
        'Qux',
    ],
    '2021-03-02' => [
        'Quux',
    ],
];

$datePicker = (new Picker($_GET))
    ->addEvents($events)
;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Functional Test | PHP Date Picker</title>
        <link rel="stylesheet" href="../assets/css/picker.css">

        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
            }

            .date-picker {
                width: 300px;
                box-shadow: 0 0 8px #bbb;
            }

            .screenshot-edge {
                display: inline-block;
                border: 1px dashed;
                padding: 8px;
            }
        </style>
    </head>

    <body>
        <h1>Functional Test</h1>

        <div class="<?= $_GET['screenshot_edge'] ?? 0 ? 'screenshot-edge' : '' ?>">
            <?= $datePicker->render() ?>
        </div>
    </body>
</html>
