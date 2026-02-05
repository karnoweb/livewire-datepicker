<?php

declare(strict_types=1);
$jy = 1404;
$jm = 12;
$jd = 29;
$breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
$gy = $jy + 621;
$jp = $breaks[0];
for ($i = 1; $i < count($breaks); $i++) {
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
for ($m = 0; $m < $jm - 1; $m++) {
    $gd += $jDaysInMonth[$m];
}
echo "jp={$jp} n={$n} leapG={$leapG} march={$march} gd={$gd}\n";
// Correct: 1404/12/29 = 2026-03-19
