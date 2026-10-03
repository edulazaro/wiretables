@props(['hide' => null, 'shrink' => false, 'actions' => false, 'sortable' => null, 'sort' => '', 'direction' => '', 'method' => 'sortBy'])

{{-- `hide`: the breakpoint from which the column shows (sm, md, lg, xl). `shrink`: as wide as
     its content. `actions`: the narrow last column, its label for screen readers only.
     `sortable`: the key the Livewire component's `sortBy()` (or `method`) takes; with the
     component's `sort` and `direction`, the header is a button showing the order it is in. --}}
<th {{ $attributes->class([
    'wtb-th',
    'wtb-actions' => $actions,
    'wtb-shrink' => $shrink || $actions,
    in_array($hide, ['sm', 'md', 'lg', 'xl'], true) ? 'wtb-from-'.$hide : null,
]) }} @if ($sortable) aria-sort="{{ $sort === $sortable ? ($direction === 'asc' ? 'ascending' : 'descending') : 'none' }}" @endif>@if ($actions)<span class="wtb-sr-only">{{ $slot->isEmpty() ? __('wiretables::table.actions') : $slot }}</span>@elseif ($sortable)<button type="button" wire:click="{{ $method }}('{{ $sortable }}')" @class(['wtb-sort', 'wtb-sorted' => $sort === $sortable])>{{ $slot }}@if ($sort !== $sortable)<svg class="wtb-sort-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" /></svg>@elseif ($direction === 'asc')<svg class="wtb-sort-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18" /></svg>@else<svg class="wtb-sort-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3" /></svg>@endif</button>@else{{ $slot }}@endif</th>
