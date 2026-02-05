# karnoweb/livewire-datepicker

Datepicker component for **Livewire 3** with **Alpine.js**, supporting **Jalali (Persian)** and **Gregorian** calendars. Suitable for Laravel applications that need Persian date selection with optional Gregorian export.

---

## Features

- **Jalali & Gregorian** — Display and select dates in Persian (Jalali) or Gregorian calendar
- **Livewire 3** — Full integration with `wire:model` (single, range, multiple)
- **Alpine.js** — No jQuery; lightweight and reactive
- **Export format** — Show Jalali in UI but store Gregorian (e.g. `Y-m-d`) in database
- **Date range** — Select start and end date
- **Multiple selection** — Select multiple dates with optional `maxSelections`
- **Min/Max & disabled dates** — Limit selectable dates
- **Theme** — Light, dark, or auto (follows system)
- **RTL** — Right-to-left layout for Persian
- **Accessible** — ARIA attributes and keyboard navigation
- **Customizable** — Config file and optional view/asset publishing

---

## Requirements

- PHP ^8.2
- Laravel ^11.0 | ^12.0
- Livewire ^3.0
- Alpine.js (included with Livewire 3)

## Calendar conversion (tested packages)

- **PHP:** [morilog/jalali](https://github.com/morilog/jalali) (^3.4) — Jalali/Gregorian conversion; used by the package helpers `jalali_to_gregorian()` and `gregorian_to_jalali()`.
- **JavaScript:** [jalaali-js](https://github.com/jalaali/jalaali-js) — Borkowski algorithm; the package’s Alpine datepicker uses it via a thin wrapper. Your app must have `jalaali-js` in `package.json` and import the package’s `jalali.js` + `datepicker.js` in its Vite entry so the wrapper and datepicker are bundled.

---

## Installation

```bash
composer require karnoweb/livewire-datepicker
```

### Publish config (optional)

```bash
php artisan vendor:publish --tag=datepicker-config
```

Edit `config/datepicker.php` to set default formats, theme, first day of week, and locale strings.

### Include Alpine component (required)

The package registers a Blade component but the datepicker logic lives in Alpine. You must load the JS once in your layout (e.g. before `</body>` or in your main JS bundle).

**Option A — Vite / build step**

In `resources/js/app.js` (or your entry):

```js
import './../../vendor/karnoweb/livewire-datepicker/resources/js/datepicker.js';
```

Then ensure this built `app.js` is included in your layout (e.g. `@vite(['resources/js/app.js'])`).

**Option B — Publish assets and include script tag**

```bash
php artisan vendor:publish --tag=datepicker-assets
```

Then in your Blade layout:

```html
<script defer src="{{ asset('vendor/datepicker/datepicker.js') }}"></script>
```

Note: `datepicker.js` imports `jalali.js`; if you use a bundler, the import above is enough. If you load via `<script src="...">`, you may need to concatenate or load `jalali.js` before `datepicker.js`.

**Option C — Monorepo / local path**

If the package is in `packages/karnoweb/livewire-datepicker`, point your bundler to:

```
packages/karnoweb/livewire-datepicker/resources/js/datepicker.js
```

---

## Usage

The component is registered as **`<x-jalali-datepicker>`** to avoid collision with Mary-UI's `<x-datepicker>`.

### Basic (Jalali, single date)

```blade
<x-jalali-datepicker wire:model="birth_date" />
```

### With label and placeholder

```blade
<x-jalali-datepicker
    wire:model="birth_date"
    label="تاریخ تولد"
    placeholder="انتخاب تاریخ"
/>
```

### Gregorian calendar (display)

```blade
<x-jalali-datepicker wire:model="event_date" :jalali="false" />
```

### Show Jalali, store Gregorian

Useful when the database column is a standard date (`Y-m-d`):

```blade
<x-jalali-datepicker
    wire:model="birth_date"
    jalali
    export-calendar="gregorian"
    export-format="Y-m-d"
/>
```

### Date range

```blade
<x-jalali-datepicker wire:model="date_range" :range="true" />
```

`wire:model` will receive an object like `{ "start": "1403/01/01", "end": "1403/01/15" }` (or Gregorian if `export-calendar="gregorian"`).

### Multiple dates

```blade
<x-jalali-datepicker wire:model="selected_dates" :multiple="true" />
```

Optional: limit number of selections:

```blade
<x-jalali-datepicker wire:model="selected_dates" :multiple="true" :max-selections="5" />
```

### Min / max date

```blade
<x-jalali-datepicker
    wire:model="event_date"
    min-date="1402/01/01"
    max-date="1403/12/29"
/>
```

Use the same format as `input-format` (default `Y/m/d` for Jalali).

### Disabled dates

Pass an array of date strings in the same format:

```blade
<x-jalali-datepicker
    wire:model="event_date"
    :disabled-dates="['1403/07/01', '1403/07/02']"
/>
```

### Custom formats

- **input-format** — Format shown in the input and used for min/max/disabled (e.g. `Y/m/d`, `d/m/Y`).
- **export-format** — Format sent to Livewire (e.g. `Y-m-d`, `Y/m/d`).

```blade
<x-jalali-datepicker
    wire:model="event_date"
    input-format="Y/m/d"
    export-format="Y-m-d"
/>
```

### Theme

- `light` / `dark` / `auto` (default from config). Override:

```blade
<x-jalali-datepicker wire:model="event_date" theme="dark" />
```

### Position

Dropdown position (e.g. `bottom-start`, `top-end`):

```blade
<x-jalali-datepicker wire:model="event_date" position="top-start" />
```

### Inline

Show calendar always open (no dropdown):

```blade
<x-jalali-datepicker wire:model="event_date" :inline="true" />
```

### Required / disabled

```blade
<x-jalali-datepicker wire:model="event_date" :required="true" />
<x-jalali-datepicker wire:model="event_date" :disabled="true" />
```

### Default value

Set initial value (same format as export):

```blade
<x-jalali-datepicker wire:model="event_date" default="1403/06/15" />
```

---

## Component attributes

| Attribute          | Type    | Default        | Description |
|--------------------|---------|----------------|-------------|
| `wire:model`       | string  | —              | Livewire property (required for binding) |
| `id`               | string  | auto           | Unique ID for the wrapper |
| `label`            | string  | null           | Label text above the input |
| `jalali`           | bool    | true           | Use Jalali (true) or Gregorian (false) |
| `range`            | bool    | false          | Enable date range selection |
| `multiple`         | bool    | false          | Allow multiple date selection |
| `required`         | bool    | false          | HTML required |
| `disabled`         | bool    | false          | Disable input |
| `inline`           | bool    | false          | Always show calendar (no dropdown) |
| `min-date`         | string  | null           | Minimum selectable date |
| `max-date`         | string  | null           | Maximum selectable date |
| `disabled-dates`   | array   | []             | List of disabled date strings |
| `input-format`     | string  | from config    | Display/input format (e.g. Y/m/d) |
| `export-format`    | string  | from config    | Value format sent to Livewire (e.g. Y-m-d) |
| `export-calendar`  | string  | same           | `same` or `gregorian` (when jalali=true) |
| `placeholder`      | string  | locale default | Input placeholder |
| `default`          | string  | null           | Initial value |
| `theme`            | string  | from config    | light / dark / auto |
| `position`         | string  | bottom-start   | Dropdown position |
| `max-selections`   | int     | null           | Max dates when multiple=true |

---

## Configuration

After publishing config (`config/datepicker.php`):

- **default_calendar** — `jalali` or `gregorian`
- **formats.input** — Default input/display format (e.g. `Y/m/d`)
- **formats.export** — Default export format (e.g. `Y-m-d`)
- **jalali** / **gregorian** — Month and weekday names for i18n
- **theme** — `auto`, `light`, or `dark`
- **first_day_of_week** — 0 (Sunday) or 6 (Saturday) for Gregorian/Jalali
- **holidays** — Reserved for future use

---

## Helper functions (PHP)

The package provides two helpers for server-side conversion:

```php
use function Karnoweb\LivewireDatepicker\gregorian_to_jalali;
use function Karnoweb\LivewireDatepicker\jalali_to_gregorian;

// Gregorian to Jalali: year, month, day
$j = gregorian_to_jalali(2024, 3, 20);
// ['year' => 1403, 'month' => 1, 'day' => 1]

// Jalali to Gregorian
$g = jalali_to_gregorian(1403, 1, 1);
// ['year' => 2024, 'month' => 3, 'day' => 20]
```

---

## Publishing views / assets

- **Config:** `php artisan vendor:publish --tag=datepicker-config`
- **Views:** `php artisan vendor:publish --tag=datepicker-views` → customize Blade under `resources/views/vendor/datepicker`
- **Assets:** `php artisan vendor:publish --tag=datepicker-assets` → copies JS to `public/vendor/datepicker`

---

## Namespace

- **Package:** `karnoweb/livewire-datepicker`
- **PHP namespace:** `Karnoweb\LivewireDatepicker`
- **Blade component:** `<x-jalali-datepicker />` (prefix `jalali` to avoid Mary-UI collision)

---

## License

MIT.
