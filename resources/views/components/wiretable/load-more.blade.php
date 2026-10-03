@props(['show' => true, 'method' => 'loadMore'])

{{-- "Load more", for a list read in growing pages (`WithLoadMore`): pass it `:show="$hasMore"`.
     Busy while the next page comes, so a second click does not ask twice. --}}
@if ($show)
    <div {{ $attributes->class('wtb-load-more') }}>
        <button type="button" class="wtb-load-more-button" wire:click="{{ $method }}" wire:loading.attr="disabled" wire:target="{{ $method }}">
            <span wire:loading.remove wire:target="{{ $method }}">{{ $slot->isEmpty() ? __('wiretables::table.load-more') : $slot }}</span>
            <svg wire:loading wire:target="{{ $method }}" class="wtb-spinner" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="3" opacity="0.25" /><path d="M21 12a9 9 0 0 0-9-9" stroke="currentColor" stroke-width="3" stroke-linecap="round" /></svg>
        </button>
    </div>
@endif
