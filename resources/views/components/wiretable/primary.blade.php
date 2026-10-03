@props(['title', 'subtitle' => null, 'href' => null, 'action' => null, 'navigate' => false])

{{-- The first cell's content: the record's name (a link when it opens somewhere, `navigate`
     for Livewire's SPA navigation, or a button running `action`, an Alpine expression, when it
     opens in place), a muted second line, an optional `leading` slot (an avatar, a thumbnail)
     and, in the default slot, what the hidden columns show on narrow screens. --}}
<div {{ $attributes->class('wtb-primary') }}>
    {{ $leading ?? '' }}

    <div class="wtb-primary-body">
        @if ($href)
            <a href="{{ $href }}" class="wtb-title wtb-title-link"@if ($navigate) wire:navigate @endif>{{ $title }}</a>
        @elseif ($action)
            <button type="button" x-on:click="{{ $action }}" class="wtb-title wtb-title-button">{{ $title }}</button>
        @else
            <p class="wtb-title wtb-title-text">{{ $title }}</p>
        @endif

        @if (filled($subtitle))
            <p class="wtb-subtitle">{{ $subtitle }}</p>
        @endif

        {{ $slot }}
    </div>
</div>
