<?php

declare(strict_types=1);

namespace Karnoweb\LivewireDatepicker;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DatepickerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datepicker.php', 'datepicker');
    }

    public function boot(): void
    {
        $this->registerScriptRoute();

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

        // Use prefix "jalali" to avoid collision with Mary-UI's <x-datepicker> → <x-jalali-datepicker>
        $this->loadViewComponentsAs('jalali', [
            View\Components\Datepicker::class,
        ]);
    }

    protected function registerScriptRoute(): void
    {
        Route::get(config('datepicker.script_url', '/vendor/livewire-datepicker/datepicker.js'), function () {
            $path = __DIR__ . '/../dist/datepicker.js';
            if (! is_file($path)) {
                abort(404, 'Livewire Datepicker script not built. Run npm install && npm run build in the package directory.');
            }

            return response()->file($path, ['Content-Type' => 'application/javascript']);
        })->name('livewire-datepicker.script');
    }
}
