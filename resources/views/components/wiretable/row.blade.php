@props(['href' => null, 'navigate' => false, 'action' => null])
@aware(['expandable' => false])
{{-- A row, and optionally a clickable one: `href` opens a page (`navigate` without a reload),
     `action` runs an Alpine expression. A click on anything interactive inside the row (a
     link, a button such as the menu's, a field, a label, or anything marked
     data-wtb-ignore) does its own job and not the row's, and so does a click that ends a
     text selection. Ctrl, Cmd or the middle button open `href` in a new tab. Enter opens
     it from the keyboard. --}}
@if ($href || $action)
<tr {{ $attributes->class(['wtb-row', 'wtb-row-link']) }} tabindex="0" x-data="{{ $expandable ? '{ open: false }' : '{}' }}"@if ($expandable) x-bind:class="open && 'wtb-open'"@endif
    x-on:click="if ($event.target.closest('a, button, input, select, textarea, label, summary, [contenteditable], [data-wtb-ignore]') || String(window.getSelection()) !== '') return; @if ($href) if ($event.ctrlKey || $event.metaKey) { window.open(@js($href), '_blank'); return } @if ($navigate) window.Livewire ? Livewire.navigate(@js($href)) : (window.location.href = @js($href)) @else window.location.href = @js($href) @endif @else {{ $action }} @endif"
    @if ($href) x-on:auxclick="if ($event.button === 1 && ! $event.target.closest('a, button')) window.open(@js($href), '_blank')" @endif
    x-on:keydown.enter.self="@if ($href) @if ($navigate) window.Livewire ? Livewire.navigate(@js($href)) : (window.location.href = @js($href)) @else window.location.href = @js($href) @endif @else {{ $action }} @endif">{{ $slot }}</tr>
@else
<tr {{ $attributes->class('wtb-row') }}@if ($expandable) x-data="{ open: false }" x-bind:class="open && 'wtb-open'"@endif>{{ $slot }}</tr>
@endif
