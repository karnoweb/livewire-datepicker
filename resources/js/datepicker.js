/**
 * Alpine.js Datepicker Plugin - Jalali/Gregorian, Range, Multiple
 */

import Jalali from './jalali.js';

function registerDatepicker() {
    window.Alpine.data('datepicker', (config) => ({
        isOpen: false,
        view: 'days',
        currentYear: null,
        currentMonth: null,
        selectedDates: [],
        rangeStart: null,
        rangeEnd: null,
        hoverDate: null,
        inputValue: '',
        yearRangeStart: null,
        focusedDate: null,

        config: {
            jalali: true,
            range: false,
            multiple: false,
            minDate: null,
            maxDate: null,
            disabledDates: [],
            inputFormat: 'Y/m/d',
            exportFormat: 'Y-m-d',
            exportCalendar: 'same',
            theme: 'auto',
            position: 'bottom-start',
            maxSelections: null,
            i18n: {},
            firstDayOfWeek: 6,
            ...config,
        },

        init() {
            this.parseConfig();
            this.initializeDate();
            this.syncFromWireModel();
            this.setupWatchers();
            this.setupKeyboardNavigation();
        },

        parseConfig() {
            if (this.config.minDate) {
                this.config.minDateParsed = Jalali.parse(this.config.minDate, this.config.inputFormat);
            }
            if (this.config.maxDate) {
                this.config.maxDateParsed = Jalali.parse(this.config.maxDate, this.config.inputFormat);
            }
            this.config.disabledDatesParsed = (this.config.disabledDates || []).map((d) =>
                Jalali.parse(d, this.config.inputFormat)
            ).filter(Boolean);
        },

        initializeDate() {
            const today = Jalali.today(this.config.jalali);
            this.currentYear = today.year;
            this.currentMonth = today.month;
            this.yearRangeStart = Math.floor(today.year / 12) * 12;
            this.focusedDate = { ...today };
        },

        syncFromWireModel() {
            const wireModel = this.$el.getAttribute('wire:model') || this.$el.getAttribute('wire:model.live') || this.$el.getAttribute('wire:model.blur');
            const wireValue = wireModel && this.$wire ? this.$wire.get(wireModel) : (this.$el.querySelector('input[type="hidden"]')?.value ?? null);
            if (wireValue === undefined || wireValue === null || wireValue === '') return;

            if (this.config.range) {
                this.parseRangeValue(wireValue);
            } else if (this.config.multiple) {
                this.parseMultipleValue(wireValue);
            } else {
                this.parseSingleValue(wireValue);
            }
            this.updateInputDisplay();
        },

        parseSingleValue(value) {
            const date = Jalali.parse(value, this.config.exportFormat);
            if (!date) return;
            let displayDate = date;
            if (this.config.exportCalendar === 'gregorian' && this.config.jalali) {
                displayDate = Jalali.toJalali(date.year, date.month, date.day);
            }
            this.selectedDates = [{ ...displayDate }];
            this.currentYear = displayDate.year;
            this.currentMonth = displayDate.month;
        },

        parseRangeValue(value) {
            try {
                const parsed = typeof value === 'string' ? JSON.parse(value) : value;
                if (parsed?.start) {
                    let d = Jalali.parse(parsed.start, this.config.exportFormat);
                    if (d && this.config.exportCalendar === 'gregorian' && this.config.jalali) d = Jalali.toJalali(d.year, d.month, d.day);
                    this.rangeStart = d;
                }
                if (parsed?.end) {
                    let d = Jalali.parse(parsed.end, this.config.exportFormat);
                    if (d && this.config.exportCalendar === 'gregorian' && this.config.jalali) d = Jalali.toJalali(d.year, d.month, d.day);
                    this.rangeEnd = d;
                }
                if (this.rangeStart) {
                    this.currentYear = this.rangeStart.year;
                    this.currentMonth = this.rangeStart.month;
                }
            } catch (e) {}
        },

        parseMultipleValue(value) {
            try {
                const parsed = typeof value === 'string' ? JSON.parse(value) : value;
                if (Array.isArray(parsed)) {
                    this.selectedDates = parsed.map((d) => {
                        let date = Jalali.parse(d, this.config.exportFormat);
                        if (date && this.config.exportCalendar === 'gregorian' && this.config.jalali) {
                            date = Jalali.toJalali(date.year, date.month, date.day);
                        }
                        return date;
                    }).filter(Boolean);
                }
            } catch (e) {}
        },

        setupWatchers() {
            this.$watch('selectedDates', () => this.emitValue());
            this.$watch('rangeStart', () => this.emitValue());
            this.$watch('rangeEnd', () => this.emitValue());
        },

        setupKeyboardNavigation() {
            this.$el.addEventListener('keydown', (e) => {
                if (!this.isOpen) {
                    if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); this.open(); }
                    return;
                }
                switch (e.key) {
                    case 'Escape': e.preventDefault(); this.close(); break;
                    case 'ArrowLeft': e.preventDefault(); this.moveFocus(this.config.jalali ? 1 : -1); break;
                    case 'ArrowRight': e.preventDefault(); this.moveFocus(this.config.jalali ? -1 : 1); break;
                    case 'ArrowUp': e.preventDefault(); this.moveFocus(-7); break;
                    case 'ArrowDown': e.preventDefault(); this.moveFocus(7); break;
                    case 'Enter': case ' ': e.preventDefault(); if (this.focusedDate) this.selectDate(this.focusedDate); break;
                    case 'PageUp': e.preventDefault(); e.shiftKey ? this.prevYear() : this.prevMonth(); break;
                    case 'PageDown': e.preventDefault(); e.shiftKey ? this.nextYear() : this.nextMonth(); break;
                }
            });
        },

        moveFocus(days) {
            if (!this.focusedDate) {
                this.focusedDate = Jalali.today(this.config.jalali);
                return;
            }
            let { year, month, day } = this.focusedDate;
            const daysInMonthFn = this.config.jalali ? Jalali.jalaliMonthDays.bind(Jalali) : Jalali.gregorianMonthDays.bind(Jalali);
            day += days;
            while (day < 1) {
                month--;
                if (month < 1) { month = 12; year--; }
                day += daysInMonthFn(year, month);
            }
            while (day > daysInMonthFn(year, month)) {
                day -= daysInMonthFn(year, month);
                month++;
                if (month > 12) { month = 1; year++; }
            }
            this.focusedDate = { year, month, day };
            if (month !== this.currentMonth || year !== this.currentYear) {
                this.currentMonth = month;
                this.currentYear = year;
            }
        },

        get weeks() {
            const daysInMonth = this.config.jalali
                ? Jalali.jalaliMonthDays(this.currentYear, this.currentMonth)
                : Jalali.gregorianMonthDays(this.currentYear, this.currentMonth);
            const firstDay = { year: this.currentYear, month: this.currentMonth, day: 1 };
            let startWeekDay = Jalali.weekDay(firstDay, this.config.jalali);
            startWeekDay = (startWeekDay - this.config.firstDayOfWeek + 7) % 7;

            const prevMonth = this.currentMonth === 1 ? 12 : this.currentMonth - 1;
            const prevYear = this.currentMonth === 1 ? this.currentYear - 1 : this.currentYear;
            const prevMonthDays = this.config.jalali
                ? Jalali.jalaliMonthDays(prevYear, prevMonth)
                : Jalali.gregorianMonthDays(prevYear, prevMonth);

            const days = [];
            for (let i = startWeekDay - 1; i >= 0; i--) {
                days.push({
                    date: { year: prevYear, month: prevMonth, day: prevMonthDays - i },
                    isCurrentMonth: false, isToday: false, isSelected: false, isDisabled: true, isInRange: false, isRangeStart: false, isRangeEnd: false,
                });
            }

            const today = Jalali.today(this.config.jalali);
            for (let d = 1; d <= daysInMonth; d++) {
                const date = { year: this.currentYear, month: this.currentMonth, day: d };
                days.push({
                    date,
                    isCurrentMonth: true,
                    isToday: this.isSameDate(date, today),
                    isSelected: this.isSelected(date),
                    isDisabled: this.isDisabled(date),
                    isInRange: this.isInRange(date),
                    isRangeStart: this.isRangeStart(date),
                    isRangeEnd: this.isRangeEnd(date),
                    isFocused: this.focusedDate && this.isSameDate(date, this.focusedDate),
                });
            }

            const nextMonth = this.currentMonth === 12 ? 1 : this.currentMonth + 1;
            const nextYear = this.currentMonth === 12 ? this.currentYear + 1 : this.currentYear;
            let nextDay = 1;
            while (days.length % 7 !== 0 || days.length < 42) {
                days.push({
                    date: { year: nextYear, month: nextMonth, day: nextDay++ },
                    isCurrentMonth: false, isToday: false, isSelected: false, isDisabled: true, isInRange: false, isRangeStart: false, isRangeEnd: false,
                });
            }

            const weeks = [];
            for (let i = 0; i < days.length; i += 7) weeks.push(days.slice(i, i + 7));
            return weeks;
        },

        get weekDays() {
            const days = [...(this.config.i18n.weekdays || [])];
            const first = this.config.firstDayOfWeek;
            return [...days.slice(first), ...days.slice(0, first)];
        },

        get months() {
            return Object.entries(this.config.i18n.months || {}).map(([num, name]) => ({
                num: parseInt(num, 10),
                name,
                isSelected: parseInt(num, 10) === this.currentMonth,
                isDisabled: this.isMonthDisabled(parseInt(num, 10)),
            }));
        },

        get years() {
            const years = [];
            for (let i = this.yearRangeStart; i < this.yearRangeStart + 12; i++) {
                years.push({ year: i, isSelected: i === this.currentYear, isDisabled: this.isYearDisabled(i) });
            }
            return years;
        },

        get displayValue() {
            if (this.config.range) {
                if (this.rangeStart && this.rangeEnd) return `${Jalali.format(this.rangeStart, this.config.inputFormat)} - ${Jalali.format(this.rangeEnd, this.config.inputFormat)}`;
                if (this.rangeStart) return Jalali.format(this.rangeStart, this.config.inputFormat);
                return '';
            }
            if (this.config.multiple) {
                return this.selectedDates.map((d) => Jalali.format(d, this.config.inputFormat)).join('، ');
            }
            return this.selectedDates.length ? Jalali.format(this.selectedDates[0], this.config.inputFormat) : '';
        },

        get currentMonthName() {
            return (this.config.i18n.months && this.config.i18n.months[this.currentMonth]) || '';
        },

        selectDate(date) {
            if (this.isDisabled(date)) return;
            if (this.config.range) this.selectRangeDate(date);
            else if (this.config.multiple) this.selectMultipleDate(date);
            else this.selectSingleDate(date);
        },

        selectSingleDate(date) {
            this.selectedDates = [{ ...date }];
            this.updateInputDisplay();
            this.close();
        },

        selectRangeDate(date) {
            if (!this.rangeStart || (this.rangeStart && this.rangeEnd)) {
                this.rangeStart = { ...date };
                this.rangeEnd = null;
            } else {
                if (Jalali.compare(date, this.rangeStart) < 0) {
                    this.rangeEnd = { ...this.rangeStart };
                    this.rangeStart = { ...date };
                } else this.rangeEnd = { ...date };
                this.updateInputDisplay();
                this.close();
            }
        },

        selectMultipleDate(date) {
            const index = this.selectedDates.findIndex((d) => this.isSameDate(d, date));
            if (index > -1) this.selectedDates.splice(index, 1);
            else {
                if (this.config.maxSelections && this.selectedDates.length >= this.config.maxSelections) return;
                this.selectedDates.push({ ...date });
            }
            this.updateInputDisplay();
        },

        isSameDate(d1, d2) {
            return d1 && d2 && d1.year === d2.year && d1.month === d2.month && d1.day === d2.day;
        },

        isSelected(date) {
            if (this.config.range) return (this.rangeStart && this.isSameDate(date, this.rangeStart)) || (this.rangeEnd && this.isSameDate(date, this.rangeEnd));
            return this.selectedDates.some((d) => this.isSameDate(d, date));
        },

        isInRange(date) {
            if (!this.config.range || !this.rangeStart) return false;
            const end = this.rangeEnd || this.hoverDate;
            if (!end) return false;
            let start = this.rangeStart;
            let endDate = end;
            if (Jalali.compare(start, endDate) > 0) [start, endDate] = [endDate, start];
            return Jalali.compare(date, start) >= 0 && Jalali.compare(date, endDate) <= 0;
        },

        isRangeStart(date) { return this.rangeStart && this.isSameDate(date, this.rangeStart); },
        isRangeEnd(date) { return this.rangeEnd && this.isSameDate(date, this.rangeEnd); },

        isDisabled(date) {
            if (this.config.minDateParsed && Jalali.compare(date, this.config.minDateParsed) < 0) return true;
            if (this.config.maxDateParsed && Jalali.compare(date, this.config.maxDateParsed) > 0) return true;
            return (this.config.disabledDatesParsed || []).some((d) => this.isSameDate(d, date));
        },

        isMonthDisabled(month) {
            if (!this.config.minDateParsed && !this.config.maxDateParsed) return false;
            const firstDay = { year: this.currentYear, month, day: 1 };
            const lastDay = { year: this.currentYear, month, day: this.config.jalali ? Jalali.jalaliMonthDays(this.currentYear, month) : Jalali.gregorianMonthDays(this.currentYear, month) };
            if (this.config.minDateParsed && Jalali.compare(lastDay, this.config.minDateParsed) < 0) return true;
            if (this.config.maxDateParsed && Jalali.compare(firstDay, this.config.maxDateParsed) > 0) return true;
            return false;
        },

        isYearDisabled(year) {
            if (!this.config.minDateParsed && !this.config.maxDateParsed) return false;
            if (this.config.minDateParsed && year < this.config.minDateParsed.year) return true;
            if (this.config.maxDateParsed && year > this.config.maxDateParsed.year) return true;
            return false;
        },

        prevMonth() {
            if (this.currentMonth === 1) { this.currentMonth = 12; this.currentYear--; } else this.currentMonth--;
        },
        nextMonth() {
            if (this.currentMonth === 12) { this.currentMonth = 1; this.currentYear++; } else this.currentMonth++;
        },
        prevYear() { this.currentYear--; },
        nextYear() { this.currentYear++; },
        prevYearRange() { this.yearRangeStart -= 12; },
        nextYearRange() { this.yearRangeStart += 12; },
        selectMonth(month) { this.currentMonth = month; this.view = 'days'; },
        selectYear(year) { this.currentYear = year; this.view = 'months'; },
        goToToday() {
            const today = Jalali.today(this.config.jalali);
            this.currentYear = today.year;
            this.currentMonth = today.month;
            this.view = 'days';
        },

        open() {
            if (this.config.disabled) return;
            this.isOpen = true;
            this.$nextTick(() => this.positionDropdown());
        },
        close() { this.isOpen = false; this.view = 'days'; this.hoverDate = null; },
        toggle() { this.isOpen ? this.close() : this.open(); },

        positionDropdown() {
            const dropdown = this.$refs.dropdown;
            const trigger = this.$refs.trigger;
            if (!dropdown || !trigger) return;
            const triggerRect = trigger.getBoundingClientRect();
            const dropdownRect = dropdown.getBoundingClientRect();
            const viewportHeight = window.innerHeight;
            const spaceBelow = viewportHeight - triggerRect.bottom;
            const spaceAbove = triggerRect.top;
            if (spaceBelow < dropdownRect.height && spaceAbove > spaceBelow) {
                dropdown.style.bottom = `${trigger.offsetHeight + 4}px`;
                dropdown.style.top = 'auto';
            } else {
                dropdown.style.top = `${trigger.offsetHeight + 4}px`;
                dropdown.style.bottom = 'auto';
            }
            const isRTL = this.config.jalali || document.documentElement.dir === 'rtl';
            if (isRTL) { dropdown.style.right = '0'; dropdown.style.left = 'auto'; }
            else { dropdown.style.left = '0'; dropdown.style.right = 'auto'; }
        },

        emitValue() {
            let value;
            if (this.config.range) {
                value = JSON.stringify({
                    start: this.rangeStart ? this.formatForExport(this.rangeStart) : null,
                    end: this.rangeEnd ? this.formatForExport(this.rangeEnd) : null,
                });
            } else if (this.config.multiple) {
                value = JSON.stringify(this.selectedDates.map((d) => this.formatForExport(d)));
            } else {
                value = this.selectedDates.length ? this.formatForExport(this.selectedDates[0]) : null;
            }

            const wireModel = this.$el.getAttribute('wire:model') || this.$el.getAttribute('wire:model.live') || this.$el.getAttribute('wire:model.blur');
            if (wireModel && this.$wire) this.$wire.set(wireModel, value);

            const hiddenInput = this.$el.querySelector('input[type="hidden"]');
            if (hiddenInput) {
                hiddenInput.value = value ?? '';
                hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
            }
            this.$dispatch('datepicker-change', { value });
        },

        formatForExport(date) {
            let exportDate = date;
            if (this.config.exportCalendar === 'gregorian' && this.config.jalali) {
                exportDate = Jalali.toGregorian(date.year, date.month, date.day);
            } else if (this.config.exportCalendar === 'jalali' && !this.config.jalali) {
                exportDate = Jalali.toJalali(date.year, date.month, date.day);
            }
            return Jalali.format(exportDate, this.config.exportFormat);
        },

        updateInputDisplay() { this.inputValue = this.displayValue; },

        handleInput(e) {
            const value = e.target.value;
            if (!value) { this.clear(); return; }
            const date = Jalali.parse(value, this.config.inputFormat);
            if (date && !this.isDisabled(date)) {
                this.selectedDates = [date];
                this.currentYear = date.year;
                this.currentMonth = date.month;
                this.updateInputDisplay();
                this.emitValue();
            }
        },

        clear() {
            this.selectedDates = [];
            this.rangeStart = null;
            this.rangeEnd = null;
            this.inputValue = '';
            this.emitValue();
        },

        handleDayHover(date) {
            if (this.config.range && this.rangeStart && !this.rangeEnd) this.hoverDate = date;
        },

        // auto = follow app theme (.dark on html/body); no class so CSS .dark .dp-wrapper applies
        get themeClass() {
            if (this.config.theme === 'auto') return '';
            return this.config.theme === 'dark' ? 'dp-dark' : '';
        },
    }));
}

document.addEventListener('alpine:init', registerDatepicker);
if (typeof window.Alpine !== 'undefined' && window.Alpine.version) {
    registerDatepicker();
}
