<?php

declare(strict_types=1);

return [
    'default_calendar' => 'jalali',

    'formats' => [
        'input' => 'Y/m/d',
        'export' => 'Y-m-d',
    ],

    'jalali' => [
        'months' => [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد',
            4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
            7 => 'مهر', 8 => 'آبان', 9 => 'آذر',
            10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
        ],
        'weekdays' => [
            'ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج',
        ],
        'weekdays_full' => [
            'شنبه', 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنج‌شنبه', 'جمعه',
        ],
    ],

    'gregorian' => [
        'months' => [
            1 => 'January', 2 => 'February', 3 => 'March',
            4 => 'April', 5 => 'May', 6 => 'June',
            7 => 'July', 8 => 'August', 9 => 'September',
            10 => 'October', 11 => 'November', 12 => 'December',
        ],
        'weekdays' => [
            'Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa',
        ],
    ],

    'theme' => 'auto',

    'first_day_of_week' => [
        'jalali' => 6,
        'gregorian' => 0,
    ],

    'holidays' => [],
];
