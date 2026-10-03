@props(['href' => null, 'danger' => false])

{{-- One line of the row menu: a link with `href`, otherwise a button (wire:click, x-on:click).
     `leading` holds an icon or a dot before the text; `danger` for what destroys or cannot be
     undone. --}}
<{{ $href ? 'a' : 'button' }} @if ($href) href="{{ $href }}" @else type="button" @endif {{ $attributes->class(['wtb-menu-item', 'wtb-danger' => $danger]) }}>
    {{ $leading ?? '' }}
    {{ $slot }}
</{{ $href ? 'a' : 'button' }}>
