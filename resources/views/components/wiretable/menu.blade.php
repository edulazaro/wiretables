@props(['label', 'align' => 'left'])

{{-- A row's "⋯". The menu is moved to <body> and anchored to its button (Alpine's x-teleport and
     x-anchor, both bundled with Livewire), because a table that scrolls sideways clips anything
     that overflows it. Choosing an item closes it. `align="right"` puts the items' text on the
     right, for an interface whose menus all read that way; left is the default and the norm. --}}
<div x-data="{ open: false }" {{ $attributes->class('wtb-menu') }}>
    <button type="button" x-ref="trigger" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()" aria-label="{{ $label }}"
            class="wtb-menu-trigger">
        <svg class="wtb-menu-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM12.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0ZM18.75 12a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" /></svg>
    </button>

    <template x-teleport="body">
        <div x-cloak x-show="open" x-anchor.bottom-end.offset.4="$refs.trigger"
             x-on:click.outside="$refs.trigger.contains($event.target) || (open = false)"
             x-on:keydown.escape.window="open = false"
             x-on:click="open = false"
             x-transition:enter.scale.95.origin.top.right.duration.100ms
             @class(['wtb-menu-panel', 'wtb-menu-right' => $align === 'right'])>
            {{ $slot }}
        </div>
    </template>
</div>
