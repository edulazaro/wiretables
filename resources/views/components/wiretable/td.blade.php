@props(['hide' => null, 'shrink' => false, 'actions' => false])

{{-- The same `hide` and `shrink` as its column's `x-wiretable.th`; `actions` for the last cell. --}}
<td {{ $attributes->class([
    'wtb-td',
    'wtb-actions' => $actions,
    'wtb-nowrap' => $shrink || $actions,
    in_array($hide, ['sm', 'md', 'lg', 'xl'], true) ? 'wtb-from-'.$hide : null,
]) }}>{{ $slot }}</td>
