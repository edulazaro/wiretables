![Wiretables](art/banner.png)

# wiretables

Tables for Laravel, Livewire and Alpine: Blade components that read the same on a phone and on a desktop, header sorting, and a row actions menu that opens above everything, so no scrolling table or modal cuts it off. Pure CSS, no Tailwind or Bootstrap needed. Part of the `wire*` family: themeable through the shared `data-wire-theme` attribute, visually coherent with [wiremodal](https://github.com/edulazaro/wiremodal), [wiretoast](https://github.com/edulazaro/wiretoast), [wirepicker](https://github.com/edulazaro/wirepicker), [wirecookies](https://github.com/edulazaro/wirecookies) and [wirebug](https://github.com/edulazaro/wirebug).

Blade markup you write · columns that hide instead of scrolling · sorting · row actions menu · 11 themes · 0 runtime deps.

It is not a table engine configured from PHP arrays: each table is written in Blade, cell by cell, so a cell that needs something unusual is just Blade. The package brings the pieces, their look, and the behaviour every list repeats.

## Install

```bash
composer require edulazaro/wiretables
php artisan vendor:publish --tag=wiretables-assets
```

Add to your layout, the core and, if you want one, a theme:

```blade
<link rel="stylesheet" href="{{ asset('vendor/wiretables/css/wiretables-core.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/wiretables/css/themes/studio.css') }}">
```

Or import into your Vite bundle:

```css
@import "../../vendor/edulazaro/wiretables/resources/css/wiretables-core.css";
@import "../../vendor/edulazaro/wiretables/resources/css/themes/studio.css";
```

`wiretables.css` holds the core and every theme in one file, for when you would rather not choose. Adding themes to the package never makes `wiretables-core.css` bigger.

The row menu needs Alpine with its anchor plugin, both bundled with Livewire 3 and 4.

## A table

```blade
<x-wiretable>
    <x-slot:head>
        <x-wiretable.th>Event</x-wiretable.th>
        <x-wiretable.th hide="sm" shrink>Status</x-wiretable.th>
        <x-wiretable.th hide="md" shrink>Date</x-wiretable.th>
        <x-wiretable.th actions />
    </x-slot:head>

    @forelse ($projects as $project)
        <x-wiretable.row wire:key="project-{{ $project->id }}">
            <x-wiretable.td>
                <x-wiretable.primary :title="$project->name" :subtitle="$project->email" :href="route('projects.show', $project)">
                    {{-- What the hidden columns show on a phone. --}}
                    <p class="md:hidden">{{ $project->status }} · {{ $project->date }}</p>
                </x-wiretable.primary>
            </x-wiretable.td>
            <x-wiretable.td hide="sm" shrink>{{ $project->status }}</x-wiretable.td>
            <x-wiretable.td hide="md" shrink>{{ $project->date }}</x-wiretable.td>
            <x-wiretable.td actions>
                <x-wiretable.menu :label="'Actions of '.$project->name">
                    <x-wiretable.menu-item :href="route('projects.show', $project)">Open</x-wiretable.menu-item>
                </x-wiretable.menu>
            </x-wiretable.td>
        </x-wiretable.row>
    @empty
        <x-wiretable.empty colspan="4">Nothing yet.</x-wiretable.empty>
    @endforelse
</x-wiretable>
```

The first column holds the record and takes the room left; the rest fit their content (`shrink`). A column with `hide="md"` shows from that breakpoint up (`sm`, `md`, `lg`, `xl`, Tailwind's widths), and its `th` and `td` take the same value. Nothing scrolls sideways: what a phone cannot show, the first cell repeats in its slot.

## The first cell

`<x-wiretable.primary>` is the record: its name as a link (`href`), as a button running an Alpine expression (`action`, to open it in place), or as plain text; a muted `subtitle`; a `leading` slot for an avatar or a thumbnail; and, in its default slot, whatever the hidden columns show on narrow screens. On a desktop, hovering the row underlines the name.

```blade
<x-wiretable.primary :title="$user->name" :subtitle="$user->email" action="$dispatch('user-open', { id: {{ $user->id }} })">
    <x-slot:leading><img src="{{ $user->avatar }}" alt="" width="32" height="32"></x-slot:leading>
</x-wiretable.primary>
```

## Sorting

Give a header a `sortable` key and the component's `sort` and `direction`, and it becomes a button that shows the order it is in:

```blade
<x-wiretable.th hide="md" shrink sortable="date" :sort="$sort" :direction="$direction">Date</x-wiretable.th>
```

The Livewire side is the `WithSorting` trait: it holds `sort` and `direction` (in the URL, so a sorted list can be reloaded or shared), and its `sortBy()` cycles ascending, descending and back to the list's own order, then goes back to the first page. Only the keys `sortable()` returns are accepted, since the client can send any string:

```php
use EduLazaro\Wiretables\Concerns\WithSorting;

class Projects extends Component
{
    use WithPagination, WithSorting;

    protected function sortable(): array
    {
        return ['date', 'received'];
    }

    public function render()
    {
        $column = ['date' => 'starts_on', 'received' => 'created_at'][$this->sort] ?? null;

        $projects = Project::query()
            ->when($column, fn ($query) => $query->orderBy($column, $this->direction))
            ->paginate(25);

        return view('livewire.projects', compact('projects'));
    }
}
```

The query maps the key to a column itself. A header can call another method with `method="order"`.

## The row menu

`<x-wiretable.menu :label="…">` is the row's "⋯": a button and a menu of actions.

```blade
<x-wiretable.menu :label="'Actions of '.$project->name">
    <x-wiretable.menu-item :href="route('projects.show', $project)">
        <x-slot:leading><svg>…</svg></x-slot:leading>
        Open
    </x-wiretable.menu-item>
    <x-wiretable.menu-item wire:click="duplicate({{ $project->id }})">Duplicate</x-wiretable.menu-item>
    <x-wiretable.menu-separator />
    <x-wiretable.menu-item wire:click="confirmDelete({{ $project->id }})" danger>Delete…</x-wiretable.menu-item>
</x-wiretable.menu>
```

A `menu-item` is a link with `href` and a button otherwise (`wire:click`, `x-on:click`); `leading` holds an icon (18 px) or a dot before the text, and `danger` paints what destroys or cannot be undone. `menu-separator` splits the groups. The menu is moved to `<body>` and anchored to its button, so a table that scrolls, a card or a modal never clips it; choosing an item, a click outside or Escape closes it. Actions per row live here rather than as loose buttons, which do not fit on a phone.

## Options

| Component | Attribute | |
|---|---|---|
| `x-wiretable.th` | `hide` | `sm`, `md`, `lg` or `xl`: the breakpoint from which the column shows |
| | `shrink` | As wide as its content |
| | `actions` | The narrow last column; its text is for screen readers only ("Actions" by default) |
| | `sortable`, `sort`, `direction`, `method` | Header sorting, above |
| `x-wiretable.td` | `hide`, `shrink`, `actions` | The same as its column's header |
| `x-wiretable.primary` | `title`, `subtitle`, `href`, `action` | The record, above; slots `leading` and default |
| `x-wiretable.empty` | `colspan` | The row shown when the list is empty |
| `x-wiretable.menu` | `label` | The button's accessible name |
| `x-wiretable.menu-item` | `href`, `danger` | A link or a button; slot `leading` for an icon |
| `x-wiretable.menu-separator` | | A line between groups |

Any other attribute (`class`, `wire:key`, `data-*`) lands on the element.

## Themes

Pick the theme once on `<html>`, shared with the rest of the family, and import its file:

```html
<html data-wire-theme="studio">
```

| Theme | Vibe |
|---|---|
| default | Neutral light/dark, follows the system |
| `soft` | Tinted with the accent |
| `glass` | Frosted backdrop blur |
| `gradient` | The header on the accent's gradient, white text |
| `neon` | Dark, glowing accent, mono font |
| `minimal` | No border, a stripe of accent on the left |
| `claude` | Warm minimal, Anthropic-inspired |
| `chatgpt` | Clean neutral |
| `studio` | Gray-900, sharp corners |
| `synthwave` | Retro 80s purple and magenta |
| `megaflow` | Flowbite-style: clean white card, gray header |
| `brutalist` | Black border, hard offset shadow, yellow header |

Dark mode: `data-wire-theme-mode="dark"` or a `.dark` ancestor. The row menu opens on `<body>`, so it follows a theme set on `<html>`.

## Custom look

Everything is a CSS variable. The `--wire-*` tokens are shared with the family, so a theme written for them already reaches the table; `--wtb-*` are its own:

```css
:root {
    --wtb-text: #1e2a36;
    --wtb-muted: #5e6a77;
    --wtb-line: #e3e6e1;
    --wtb-row-hover: #f9faf8;
    --wtb-radius: 6px;
}
```

| Token | |
|---|---|
| `--wtb-bg`, `--wtb-text`, `--wtb-muted`, `--wtb-faint`, `--wtb-line`, `--wtb-row-hover` | Colours: ground, ink, secondary text, the idle sort icon, lines, a hovered row |
| `--wtb-head-bg`, `--wtb-head-text`, `--wtb-head-strong` | The header, and its sorted or hovered column |
| `--wtb-radius`, `--wtb-shadow`, `--wtb-font`, `--wtb-font-size`, `--wtb-head-font-size`, `--wtb-head-line-height` | Shape and type (`--wtb-font` inherits the page's by default) |
| `--wtb-head-padding`, `--wtb-cell-padding`, `--wtb-actions-head-padding`, `--wtb-actions-cell-padding`, `--wtb-empty-padding` | Spacing |
| `--wtb-menu-bg`, `--wtb-menu-shadow`, `--wtb-menu-radius`, `--wtb-menu-width`, `--wtb-menu-font-size`, `--wtb-menu-z-index` | The row menu |
| `--wtb-menu-item-hover`, `--wtb-danger` | A hovered action, and a dangerous one |

The package sets every value it relies on (borders, margins, button resets), so a table looks the same with or without a CSS framework's reset underneath.

## Anatomy

```
.wtb-table                 the frame: radius, line, horizontal scroll as a safety net
  table.wtb-grid
    thead.wtb-head
      th.wtb-th            .wtb-shrink, .wtb-actions, .wtb-from-{sm,md,lg,xl}
        button.wtb-sort    .wtb-sorted, with .wtb-sort-icon
    tbody.wtb-body
      tr.wtb-row
        td.wtb-td          .wtb-nowrap, .wtb-actions, .wtb-from-*
          .wtb-primary     .wtb-title (-link, -button, -text), .wtb-subtitle
          .wtb-menu        .wtb-menu-trigger
      td.wtb-empty

.wtb-menu-panel            the row menu, on <body>
  .wtb-menu-item           .wtb-danger
  .wtb-menu-separator
```

## Languages

The one word of its own, the actions column's label for screen readers, ships in English and Spanish:

```bash
php artisan vendor:publish --tag=wiretables-lang
```

## Tests

```bash
composer install
vendor/bin/phpunit
```

After editing `wiretables-core.css` or a theme, rebuild the bundle with `php bin/build-css.php`; a test fails while it is out of date.

## Sponsors

wiretables is supported by the following sponsors. Thank you for keeping it growing:

<p>
  <a href="https://kenodo.com"><img src="art/logo-kenodo.png" width="24" alt="Kenodo"></a>&nbsp;<a href="https://kenodo.com">Kenodo</a>&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="https://andorradev.com"><img src="art/logo-andorradev.png" width="24" alt="AndorraDev"></a>&nbsp;<a href="https://andorradev.com">AndorraDev</a>
</p>

## Author

Created by [Edu Lazaro](https://edulazaro.com)

## License

wiretables is open-sourced software licensed under the [MIT license](LICENSE).
