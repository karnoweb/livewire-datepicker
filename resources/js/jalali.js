/**
 * Pure JavaScript Jalali Calendar Converter
 */

const Jalali = {
    jDaysInMonth: [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29],
    gDaysInMonth: [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],

    isJalaliLeap(jy) {
        const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
        let bl = breaks.length;
        let jp = breaks[0];
        let jm, jump, leap, n, i;
        for (i = 1; i < bl; i += 1) {
            jm = breaks[i];
            jump = jm - jp;
            if (jy < jm) {
                n = jy - jp;
                if (jump - n < 6) n = n - jump + ((jump + 4) / 33 | 0) * 33;
                leap = ((((n + 1) % 33) - 1) % 4) === 0;
                if (leap && (n - 1) % 33 === 3) leap = false;
                break;
            }
            jp = jm;
        }
        return leap;
    },

    isGregorianLeap(gy) {
        return (gy % 4 === 0 && gy % 100 !== 0) || (gy % 400 === 0);
    },

    jalaliMonthDays(jy, jm) {
        if (jm === 12 && this.isJalaliLeap(jy)) return 30;
        return this.jDaysInMonth[jm - 1];
    },

    gregorianMonthDays(gy, gm) {
        if (gm === 2 && this.isGregorianLeap(gy)) return 29;
        return this.gDaysInMonth[gm - 1];
    },

    toGregorian(jy, jm, jd) {
        const breaks = [-61, 9, 38, 199, 426, 686, 756, 818, 1111, 1181, 1210, 1635, 2060, 2097, 2192, 2262, 2324, 2394, 2456, 3178];
        let bl = breaks.length;
        let gy = jy + 621;
        let jp = breaks[0];
        let jm2, jump, leap, leapG, march, n, i;
        for (i = 1; i < bl; i += 1) {
            jm2 = breaks[i];
            jump = jm2 - jp;
            if (jy < jm2) break;
            jp = jm2;
        }
        n = jy - jp;
        jp = breaks[i - 1];
        leapG = Math.floor(gy / 4) - Math.floor((Math.floor(gy / 100) + 1) * 3 / 4) - 150;
        march = 20 + n - leapG;
        if (jump - n < 6) n = n - jump + Math.floor((jump + 4) / 33) * 33;
        leap = (((n + 1) % 33 - 1) % 4 === 0);
        if (leap && (n - 1) % 33 === 3) leap = false;
        if (leap && jy - jp > 0) march--;
        let gd = jd + march;
        for (let m = 0; m < jm - 1; m++) gd += this.jDaysInMonth[m];
        let gm = 3;
        while (gd > this.gregorianMonthDays(gy, gm)) {
            gd -= this.gregorianMonthDays(gy, gm);
            gm++;
            if (gm > 12) { gm = 1; gy++; }
        }
        return { year: gy, month: gm, day: gd };
    },

    toJalali(gy, gm, gd) {
        let gDayNo = gd;
        for (let i = 0; i < gm - 1; i++) {
            gDayNo += this.gDaysInMonth[i];
            if (i === 1 && this.isGregorianLeap(gy)) gDayNo++;
        }
        let jDayNo = gDayNo + (gy - 622) * 365 + Math.floor((gy - 621) / 4) - Math.floor((gy - 621) / 100) + Math.floor((gy - 621) / 400) - 79;
        let jy = 1;
        while (jDayNo > (this.isJalaliLeap(jy) ? 366 : 365)) {
            jDayNo -= this.isJalaliLeap(jy) ? 366 : 365;
            jy++;
        }
        let jm = 1;
        while (jDayNo > this.jalaliMonthDays(jy, jm)) {
            jDayNo -= this.jalaliMonthDays(jy, jm);
            jm++;
        }
        return { year: jy, month: jm, day: jDayNo };
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
