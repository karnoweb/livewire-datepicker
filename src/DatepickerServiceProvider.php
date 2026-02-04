<?php

declare(strict_types=1);

namespace Karnoweb\LivewireDatepicker;

use Illuminate\Support\ServiceProvider;

class DatepickerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datepicker.php', 'datepicker');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/datepicker.php' => config_path('datepicker.php'),
        ], 'datepicker-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/datepicker'),
        ], 'datepicker-views');

        $this->publishes([
            __DIR__ . '/../resources/js' => public_path('vendor/datepicker'),
        ], 'datepicker-assets');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'datepicker');

        $this->loadViewComponentsAs('', [
            View\Components\Datepicker::class,
        ]);
    }
}
