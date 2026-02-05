<?php

declare(strict_types=1);

use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

if ( ! function_exists('jalali_to_gregorian')) {
    /**
     * Convert Jalali date to Gregorian (via morilog/jalali).
     *
     * @return array{year: int, month: int, day: int}
     */
    function jalali_to_gregorian(int $jy, int $jm, int $jd): array
    {
        $j = new Jalalian($jy, $jm, $jd);
        $carbon = $j->toCarbon();

        return [
            'year' => (int) $carbon->year,
            'month' => (int) $carbon->month,
            'day' => (int) $carbon->day,
        ];
    }
}

if ( ! function_exists('gregorian_to_jalali')) {
    /**
     * Convert Gregorian date to Jalali (via morilog/jalali).
     *
     * @return array{year: int, month: int, day: int}
     */
    function gregorian_to_jalali(int $gy, int $gm, int $gd): array
    {
        $carbon = Carbon::createFromDate($gy, $gm, $gd);
        $j = Jalalian::fromCarbon($carbon);

        return [
            'year' => $j->getYear(),
            'month' => $j->getMonth(),
            'day' => $j->getDay(),
        ];
    }
}
