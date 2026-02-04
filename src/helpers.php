<?php

declare(strict_types=1);

if ( ! function_exists('jalali_to_gregorian')) {
    /**
     * Convert Jalali date to Gregorian.
     *
     * @return array{year: int, month: int, day: int}
     */
    function jalali_to_gregorian(int $jy, int $jm, int $jd): array
    {
        $breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];

        $gy = $jy + 621;
        $bl = count($breaks);
        $jp = $breaks[0];

        for ($i = 1; $i < $bl; $i++) {
            $jm2 = $breaks[$i];
            if ($jy < $jm2) {
                break;
            }
            $jp = $jm2;
        }

        $n = $jy - $jp;
        $leapG = (int) floor($gy / 4) - (int) floor(((int) floor($gy / 100) + 1) * 3 / 4) - 150;
        $march = 20 + $n - $leapG;

        $gd = $jd + $march;
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        for ($m = 0; $m < $jm - 1; $m++) {
            $gd += $jDaysInMonth[$m];
        }

        $gm = 3;
        $isLeap = fn ($y) => ($y % 4 === 0 && $y % 100 !== 0) || ($y % 400 === 0);

        while ($gd > ($gm === 2 && $isLeap($gy) ? 29 : $gDaysInMonth[$gm - 1])) {
            $gd -= ($gm === 2 && $isLeap($gy) ? 29 : $gDaysInMonth[$gm - 1]);
            $gm++;
            if ($gm > 12) {
                $gm = 1;
                $gy++;
            }
        }

        return ['year' => $gy, 'month' => $gm, 'day' => $gd];
    }
}

if ( ! function_exists('gregorian_to_jalali')) {
    /**
     * Convert Gregorian date to Jalali.
     *
     * @return array{year: int, month: int, day: int}
     */
    function gregorian_to_jalali(int $gy, int $gm, int $gd): array
    {
        $gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        $jDaysInMonth = [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29];

        $isGLeap = fn ($y) => ($y % 4 === 0 && $y % 100 !== 0) || ($y % 400 === 0);

        $gDayNo = $gd;
        for ($i = 0; $i < $gm - 1; $i++) {
            $gDayNo += $gDaysInMonth[$i];
            if ($i === 1 && $isGLeap($gy)) {
                $gDayNo++;
            }
        }

        $jDayNo = $gDayNo + ($gy - 622) * 365
            + (int) floor(($gy - 621) / 4)
            - (int) floor(($gy - 621) / 100)
            + (int) floor(($gy - 621) / 400)
            - 79;

        $isJLeap = function ($y) {
            $breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
            $bl = count($breaks);
            $jp = $breaks[0];

            for ($i = 1; $i < $bl; $i++) {
                $jm = $breaks[$i];
                if ($y < $jm) {
                    $n = $y - $jp;
                    $leap = ((($n + 1) % 33 - 1) % 4) === 0;
                    if ($leap && ($n - 1) % 33 === 3) {
                        $leap = false;
                    }

                    return $leap;
                }
                $jp = $jm;
            }

            return false;
        };

        $jy = 1;
        while ($jDayNo > ($isJLeap($jy) ? 366 : 365)) {
            $jDayNo -= $isJLeap($jy) ? 366 : 365;
            $jy++;
        }

        $jm = 1;
        while ($jDayNo > ($jm === 12 && $isJLeap($jy) ? 30 : $jDaysInMonth[$jm - 1])) {
            $jDayNo -= ($jm === 12 && $isJLeap($jy) ? 30 : $jDaysInMonth[$jm - 1]);
            $jm++;
        }

        return ['year' => $jy, 'month' => $jm, 'day' => $jDayNo];
    }
}
