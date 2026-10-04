@aware(['expandable' => false])
@props(['hide' => null, 'shrink' => false, 'truncate' => false, 'actions' => false, 'align' => null, 'label' => null])

{{-- The same `hide`, `shrink`, `truncate` and `align` as its column's `x-wiretable.th`;
     `actions` for the last cell. `truncate`: the text is cut with an ellipsis instead of
     widening the column, for a cell that can hold anything (a name, an address, a note); it
     undoes itself in a stacked card, where the text is read whole. `label`: what the cell is,
     shown above it when the table stacks into cards. In an expandable stacked table the actions
     cell carries the button that unfolds the row. --}}
<td {{ $attributes->class([
    'wtb-td',
    'wtb-actions' => $actions,
    'wtb-nowrap' => $shrink || $actions,
    'wtb-truncate' => $truncate,
    'wtb-right' => $align === 'right',
    'wtb-from-'.$hide => in_array($hide, ['sm', 'md', 'lg', 'xl'], true),
]) }}@if (filled($label)) data-label="{{ $label }}"@endif>@if ($actions && $expandable)<div class="wtb-actions-inner"><button type="button" class="wtb-expand" x-on:click="open = ! open" x-bind:aria-expanded="open.toString()" aria-label="{{ __('wiretables::table.more') }}"><svg class="wtb-expand-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg></button>{{ $slot }}</div>@else{{ $slot }}@endif</td>
