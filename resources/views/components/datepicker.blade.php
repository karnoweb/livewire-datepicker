@props([
    'id',
    'config',
    'jalali',
    'range',
    'multiple',
    'required',
    'disabled',
    'inline',
    'placeholder',
    'default',
    'label',
])

@php
    $fieldName = $attributes->get('name', $id);
@endphp

<div class="">
    <fieldset class="py-0 {{ $label ? 'fieldset' : '' }}">
        @if ($label ?? null)
            <legend class="fieldset-legend mb-0.5">
                {{ $label }}
                @if ($required)
                    <span class="text-error">*</span>
                @endif
            </legend>
        @endif
        <div x-data="datepicker(@js($config))" x-on:click.outside="close()" x-on:keydown.escape.window="close()"
            {{ $attributes->merge(['class' => 'dp-wrapper relative']) }} :class="themeClass" role="application"
            aria-label="{{ $jalali ? 'انتخاب تاریخ' : 'Date picker' }}">
            <input type="hidden" name="{{ $fieldName }}" x-ref="hiddenInput">

            <div x-ref="trigger"
                class="dp-trigger input input-bordered input-md w-full flex items-center gap-0 pe-0 ps-0 {{ $errors->has($fieldName) ? 'input-error' : '' }}">
                <button type="button" x-show="inputValue" x-on:click.stop="clear()"
                    class="dp-clear order-first shrink-0 p-2 text-base-content/60 hover:text-error border-0 bg-transparent cursor-pointer text-sm"
                    aria-label="{{ $jalali ? 'پاک کردن' : 'Clear' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <input type="text" x-model="inputValue" x-on:focus="open()"
                    x-on:input.debounce.500ms="handleInput($event)" placeholder="{{ $placeholder }}"
                    @if ($disabled) disabled @endif @if ($required) required @endif
                    autocomplete="off"
                    class="dp-input flex-1 min-w-0 border-0 bg-transparent outline-none focus:ring-0 px-2 py-2 text-base order-2"
                    :class="{ 'opacity-50 cursor-not-allowed': {{ $disabled ? 'true' : 'false' }} }"
                    aria-haspopup="dialog" :aria-expanded="isOpen">
                <button type="button" x-on:click="toggle()"
                    class="dp-icon order-last shrink-0 p-2 text-base-content/60 hover:text-base-content border-0 bg-transparent cursor-pointer text-sm"
                    tabindex="-1" aria-label="{{ $jalali ? 'باز کردن تقویم' : 'Open calendar' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </button>
            </div>

            <div x-ref="dropdown" x-show="isOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="dp-dropdown" role="dialog" aria-modal="true"
                aria-label="{{ $jalali ? 'تقویم' : 'Calendar' }}" x-cloak>
                <div class="dp-header">
                    <button type="button"
                        x-on:click="view === 'years' ? prevYearRange() : (view === 'months' ? prevYear() : prevMonth())"
                        class="dp-nav-btn" aria-label="{{ $jalali ? 'قبلی' : 'Previous' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="{{ $jalali ? 'M8.25 4.5l7.5 7.5-7.5 7.5' : 'M15.75 19.5L8.25 12l7.5-7.5' }}" />
                        </svg>
                    </button>
                    <div class="dp-header-title">
                        <button type="button" x-show="view === 'days'" x-on:click="view = 'months'"
                            class="dp-title-btn" x-text="currentMonthName"></button>
                        <button type="button" x-on:click="view = view === 'years' ? 'days' : 'years'"
                            class="dp-title-btn"
                            x-text="view === 'years' ? `${yearRangeStart} - ${yearRangeStart + 11}` : currentYear"></button>
                    </div>
                    <button type="button"
                        x-on:click="view === 'years' ? nextYearRange() : (view === 'months' ? nextYear() : nextMonth())"
                        class="dp-nav-btn" aria-label="{{ $jalali ? 'بعدی' : 'Next' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="{{ $jalali ? 'M15.75 19.5L8.25 12l7.5-7.5' : 'M8.25 4.5l7.5 7.5-7.5 7.5' }}" />
                        </svg>
                    </button>
                </div>

                <div x-show="view === 'days'" class="dp-body">
                    <div class="dp-weekdays">
                        <template x-for="day in weekDays" :key="day">
                            <div class="dp-weekday" x-text="day"></div>
                        </template>
                    </div>
                    <div class="dp-days">
                        <template x-for="(week, weekIndex) in weeks" :key="weekIndex">
                            <div class="dp-week">
                                <template x-for="(day, dayIndex) in week" :key="`${weekIndex}-${dayIndex}`">
                                    <button type="button" x-on:click="selectDate(day.date)"
                                        x-on:mouseenter="handleDayHover(day.date)"
                                        :disabled="day.isDisabled || !day.isCurrentMonth" class="dp-day"
                                        :class="{
                                            'dp-day-other': !day.isCurrentMonth,
                                            'dp-day-today': day.isToday,
                                            'dp-day-selected': day.isSelected,
                                            'dp-day-disabled': day.isDisabled,
                                            'dp-day-in-range': day.isInRange,
                                            'dp-day-range-start': day.isRangeStart,
                                            'dp-day-range-end': day.isRangeEnd,
                                            'dp-day-focused': day.isFocused,
                                        }"
                                        x-text="day.date.day" :aria-selected="day.isSelected"
                                        :aria-current="day.isToday ? 'date' : false"></button>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="view === 'months'" class="dp-months">
                    <template x-for="month in months" :key="month.num">
                        <button type="button" x-on:click="selectMonth(month.num)" :disabled="month.isDisabled"
                            class="dp-month"
                            :class="{ 'dp-month-selected': month.isSelected, 'dp-month-disabled': month.isDisabled }"
                            x-text="month.name"></button>
                    </template>
                </div>

                <div x-show="view === 'years'" class="dp-years">
                    <template x-for="yearItem in years" :key="yearItem.year">
                        <button type="button" x-on:click="selectYear(yearItem.year)" :disabled="yearItem.isDisabled"
                            class="dp-year"
                            :class="{ 'dp-year-selected': yearItem.isSelected, 'dp-year-disabled': yearItem.isDisabled }"
                            x-text="yearItem.year"></button>
                    </template>
                </div>

                <div class="dp-footer">
                    <button type="button" x-on:click="goToToday()"
                        class="dp-today-btn">{{ $jalali ? 'امروز' : 'Today' }}</button>
                    @if ($range || $multiple)
                        <button type="button" x-on:click="clear()"
                            class="dp-clear-btn">{{ $jalali ? 'پاک کردن' : 'Clear' }}</button>
                    @endif
                </div>
            </div>
        </div>
        @error($fieldName)
            <p class="label text-error mt-1" role="alert">{{ $message }}</p>
        @enderror
    </fieldset>
</div>

@once
    @push('style')
        <style>
            .dp-wrapper {
                --dp-primary: 59 130 246;
                --dp-primary-hover: 37 99 235;
                --dp-bg: 255 255 255;
                --dp-bg-secondary: 249 250 251;
                --dp-text: 17 24 39;
                --dp-text-secondary: 107 114 128;
                --dp-border: 229 231 235;
                --dp-hover: 243 244 246;
                --dp-today: 254 243 199;
                --dp-selected: 59 130 246;
                --dp-range: 219 234 254;
                --dp-disabled: 156 163 175;
                --dp-radius: 0.5rem;
                --dp-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
                font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
                direction: rtl;
            }

            .dp-wrapper.dp-dark {
                --dp-bg: 31 41 55;
                --dp-bg-secondary: 55 65 81;
                --dp-text: 249 250 251;
                --dp-text-secondary: 156 163 175;
                --dp-border: 75 85 99;
                --dp-hover: 55 65 81;
                --dp-today: 120 53 15;
                --dp-range: 30 58 138;
            }

            .dp-trigger:focus-within {
                outline: 2px solid var(--color-primary);
                outline-offset: 2px;
                box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primary) 30%, transparent);
            }

            .dp-dropdown {
                position: absolute;
                z-index: 50;
                min-width: 280px;
                margin-top: 0.25rem;
                background-color: rgb(var(--dp-bg));
                border: 1px solid rgb(var(--dp-border));
                border-radius: var(--dp-radius);
                box-shadow: var(--dp-shadow);
                overflow: hidden;
            }

            .dp-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.75rem;
                background-color: rgb(var(--dp-bg-secondary));
                border-bottom: 1px solid rgb(var(--dp-border));
            }

            .dp-nav-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2rem;
                height: 2rem;
                color: rgb(var(--dp-text-secondary));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-nav-btn:hover {
                background-color: rgb(var(--dp-hover));
                color: rgb(var(--dp-text));
            }

            .dp-header-title {
                display: flex;
                gap: 0.5rem;
            }

            .dp-title-btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
                font-weight: 600;
                color: rgb(var(--dp-text));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-title-btn:hover {
                background-color: rgb(var(--dp-hover));
            }

            .dp-body {
                padding: 0.5rem;
            }

            .dp-weekdays {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                margin-bottom: 0.25rem;
            }

            .dp-weekday {
                padding: 0.5rem 0;
                font-size: 0.75rem;
                font-weight: 600;
                color: rgb(var(--dp-text-secondary));
                text-align: center;
            }

            .dp-days {
                display: flex;
                flex-direction: column;
                gap: 0.125rem;
            }

            .dp-week {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 0.125rem;
            }

            .dp-day {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.25rem;
                height: 2.25rem;
                margin: auto;
                font-size: 0.875rem;
                color: rgb(var(--dp-text));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-day:hover:not(:disabled) {
                background-color: rgb(var(--dp-hover));
            }

            .dp-day-other {
                color: rgb(var(--dp-text-secondary));
                opacity: 0.3;
            }

            .dp-day-today {
                background-color: rgb(var(--dp-today));
                font-weight: 600;
            }

            .dp-day-selected {
                background-color: rgb(var(--dp-selected)) !important;
                color: white !important;
                font-weight: 600;
            }

            .dp-day-disabled {
                color: rgb(var(--dp-disabled));
                cursor: not-allowed;
                opacity: 0.5;
            }

            .dp-day-in-range {
                background-color: rgb(var(--dp-range));
                border-radius: 0;
            }

            .dp-day-range-start {
                border-radius: 0 var(--dp-radius) var(--dp-radius) 0;
            }

            .dp-day-range-end {
                border-radius: var(--dp-radius) 0 0 var(--dp-radius);
            }

            .dp-day-focused {
                outline: 2px solid rgb(var(--dp-primary));
                outline-offset: -2px;
            }

            .dp-months {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .dp-month {
                padding: 0.75rem;
                font-size: 0.875rem;
                color: rgb(var(--dp-text));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-month:hover:not(:disabled) {
                background-color: rgb(var(--dp-hover));
            }

            .dp-month-selected {
                background-color: rgb(var(--dp-selected)) !important;
                color: white !important;
                font-weight: 600;
            }

            .dp-month-disabled {
                color: rgb(var(--dp-disabled));
                cursor: not-allowed;
                opacity: 0.5;
            }

            .dp-years {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
                padding: 0.75rem;
            }

            .dp-year {
                padding: 0.75rem;
                font-size: 0.875rem;
                color: rgb(var(--dp-text));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-year:hover:not(:disabled) {
                background-color: rgb(var(--dp-hover));
            }

            .dp-year-selected {
                background-color: rgb(var(--dp-selected)) !important;
                color: white !important;
                font-weight: 600;
            }

            .dp-year-disabled {
                color: rgb(var(--dp-disabled));
                cursor: not-allowed;
                opacity: 0.5;
            }

            .dp-footer {
                display: flex;
                justify-content: space-between;
                padding: 0.5rem 0.75rem;
                background-color: rgb(var(--dp-bg-secondary));
                border-top: 1px solid rgb(var(--dp-border));
            }

            .dp-today-btn,
            .dp-clear-btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
                font-weight: 500;
                color: rgb(var(--dp-primary));
                background: none;
                border: none;
                border-radius: var(--dp-radius);
                cursor: pointer;
            }

            .dp-today-btn:hover,
            .dp-clear-btn:hover {
                background-color: rgb(var(--dp-primary) / 0.1);
            }

            @media (max-width: 640px) {
                .dp-dropdown {
                    position: fixed;
                    top: auto !important;
                    right: 0.5rem !important;
                    bottom: 0.5rem !important;
                    left: 0.5rem !important;
                    width: auto;
                    max-height: 80vh;
                    overflow-y: auto;
                }

                .dp-day {
                    width: 2.5rem;
                    height: 2.5rem;
                }
            }

            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
@endonce
