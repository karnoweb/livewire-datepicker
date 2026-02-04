<?php

declare(strict_types=1);

namespace Karnoweb\LivewireDatepicker\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Datepicker extends Component
{
    public string $id;

    public bool $jalali;

    public bool $range;

    public bool $multiple;

    public bool $required;

    public bool $disabled;

    public bool $inline;

    public ?string $minDate;

    public ?string $maxDate;

    public array $disabledDates;

    public string $inputFormat;

    public string $exportFormat;

    public string $exportCalendar;

    public ?string $placeholder;

    public ?string $default;

    public string $theme;

    public string $position;

    public ?int $maxSelections;

    public ?string $label;

    public array $config;

    public function __construct(
        ?string $id = null,
        ?string $label = null,
        bool $jalali = true,
        bool $range = false,
        bool $multiple = false,
        bool $required = false,
        bool $disabled = false,
        bool $inline = false,
        ?string $minDate = null,
        ?string $maxDate = null,
        array $disabledDates = [],
        ?string $inputFormat = null,
        ?string $exportFormat = null,
        string $exportCalendar = 'same',
        ?string $placeholder = null,
        ?string $default = null,
        ?string $theme = null,
        string $position = 'bottom-start',
        ?int $maxSelections = null,
    ) {
        $this->id = $id ?? 'dp-' . uniqid();
        $this->jalali = $jalali;
        $this->range = $range;
        $this->multiple = $multiple;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->inline = $inline;
        $this->minDate = $minDate;
        $this->maxDate = $maxDate;
        $this->disabledDates = $disabledDates;
        $this->inputFormat = $inputFormat ?? config('datepicker.formats.input');
        $this->exportFormat = $exportFormat ?? config('datepicker.formats.export');
        $this->exportCalendar = $exportCalendar;
        $this->placeholder = $placeholder ?? ($jalali ? 'انتخاب تاریخ' : 'Select date');
        $this->default = $default;
        $this->theme = $theme ?? config('datepicker.theme', 'auto');
        $this->position = $position;
        $this->maxSelections = $maxSelections;
        $this->label = $label;

        $this->config = $this->buildConfig();
    }

    protected function buildConfig(): array
    {
        return [
            'jalali' => $this->jalali,
            'range' => $this->range,
            'multiple' => $this->multiple,
            'minDate' => $this->minDate,
            'maxDate' => $this->maxDate,
            'disabledDates' => $this->disabledDates,
            'inputFormat' => $this->inputFormat,
            'exportFormat' => $this->exportFormat,
            'exportCalendar' => $this->exportCalendar,
            'theme' => $this->theme,
            'position' => $this->position,
            'maxSelections' => $this->maxSelections,
            'i18n' => $this->jalali
                ? config('datepicker.jalali')
                : config('datepicker.gregorian'),
            'firstDayOfWeek' => config('datepicker.first_day_of_week.' . ($this->jalali ? 'jalali' : 'gregorian')),
        ];
    }

    public function render(): View
    {
        return view('datepicker::components.datepicker');
    }
}
