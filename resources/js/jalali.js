/**
 * Jalali calendar wrapper around jalaali-js (Borkowski algorithm, well-tested).
 * Keeps the same API for datepicker.js; delegates conversion to jalaali-js.
 */

import jalaali from 'jalaali-js';

const gDaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

const Jalali = {
    jDaysInMonth: [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29],
    gDaysInMonth,

    isJalaliLeap(jy) {
        return jalaali.isLeapJalaaliYear(jy);
    },

    isGregorianLeap(gy) {
        return (gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0);
    },

    jalaliMonthDays(jy, jm) {
        return jalaali.jalaaliMonthLength(jy, jm);
    },

    gregorianMonthDays(gy, gm) {
        if (gm === 2 && this.isGregorianLeap(gy)) return 29;
        return gDaysInMonth[gm - 1];
    },

    toGregorian(jy, jm, jd) {
        const r = jalaali.toGregorian(jy, jm, jd);
        return { year: r.gy, month: r.gm, day: r.gd };
    },

    toJalali(gy, gm, gd) {
        const r = jalaali.toJalaali(gy, gm, gd);
        return { year: r.jy, month: r.jm, day: r.jd };
    },

    parse(str, format = 'Y-m-d') {
        if (!str) return null;
        const parts = String(str).match(/\d+/g);
        if (!parts || parts.length < 3) return null;
        const formatParts = format.match(/[Ymd]/g);
        if (!formatParts) return null;
        const result = {};
        formatParts.forEach((f, i) => {
            if (f === 'Y') result.year = parseInt(parts[i], 10);
            if (f === 'm') result.month = parseInt(parts[i], 10);
            if (f === 'd') result.day = parseInt(parts[i], 10);
        });
        return result;
    },

    format(date, format = 'Y-m-d') {
        if (!date) return '';
        const pad = (n) => n.toString().padStart(2, '0');
        return format
            .replace('Y', date.year)
            .replace('m', pad(date.month))
            .replace('d', pad(date.day));
    },

    today(isJalali = true) {
        const now = new Date();
        if (isJalali) return this.toJalali(now.getFullYear(), now.getMonth() + 1, now.getDate());
        return { year: now.getFullYear(), month: now.getMonth() + 1, day: now.getDate() };
    },

    compare(date1, date2) {
        const d1 = date1.year * 10000 + date1.month * 100 + date1.day;
        const d2 = date2.year * 10000 + date2.month * 100 + date2.day;
        return d1 - d2;
    },

    isBetween(date, min, max) {
        if (min && this.compare(date, min) < 0) return false;
        if (max && this.compare(date, max) > 0) return false;
        return true;
    },

    weekDay(date, isJalali = true) {
        let gDate;
        if (isJalali) gDate = this.toGregorian(date.year, date.month, date.day);
        else gDate = date;
        const jsDate = new Date(gDate.year, gDate.month - 1, gDate.day);
        const dow = jsDate.getDay();
        return isJalali ? (dow + 1) % 7 : dow;
    },
};

window.Jalali = Jalali;
export default Jalali;
