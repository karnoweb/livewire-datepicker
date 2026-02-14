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
- Livewire ^3.0 | ^4.0
- Alpine.js (included with Livewire)

No extra npm packages or Vite setup are required in your app — the component loads its script automatically (jalaali-js is bundled inside the package).

## Calendar conversion (tested packages)

- **PHP:** [morilog/jalali](https://github.com/morilog/jalali) (^3.4) — Jalali/Gregorian conversion; used by the package helpers `jalali_to_gregorian()` and `gregorian_to_jalali()`.
- **JavaScript:** [jalaali-js](https://github.com/jalaali/jalaali-js) — Borkowski algorithm; bundled inside the package and served automatically.

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

The datepicker script (including jalaali-js) is loaded automatically when you use `<x-jalali-datepicker>`. No npm install or Vite entry in your app is required.

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
- **Assets (optional):** `php artisan vendor:publish --tag=datepicker-assets` → copies raw JS sources to `public/vendor/datepicker` (only if you want to build them in your app; normally the bundled script is served by the package)

---

## Namespace

- **Package:** `karnoweb/livewire-datepicker`
- **PHP namespace:** `Karnoweb\LivewireDatepicker`
- **Blade component:** `<x-jalali-datepicker />` (prefix `jalali` to avoid Mary-UI collision)

---

## Releasing a new version

So clients can get the latest changes via Composer, each release must be tagged. Steps:

1. **Bump version** in `composer.json` (e.g. `1.0.2` → `1.0.3`).
2. **Rebuild the JS bundle** (if you changed `resources/js/`): run `npm install && npm run build` and commit `dist/datepicker.js`.
3. **Commit** your changes:
   ```bash
   git add .
   git commit -m "v1.0.3: your message"
   ```
3. **Create tag** (match the version in composer.json):
   ```bash
   git tag v1.0.3
   ```
4. **Push** branch and tags:
   ```bash
   git push && git push --tags
   ```

Clients using `"karnoweb/livewire-datepicker": "^1.0"` can then run `composer update karnoweb/livewire-datepicker` to get the new release.

---

## License

MIT.
