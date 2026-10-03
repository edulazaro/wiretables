@props(['stack' => null, 'expandable' => false, 'rows' => null])

{{-- A table that reads the same everywhere: rows of the record first and the rest fitted to
     their content (`x-wiretable.th` / `x-wiretable.td`). Columns hide as the screen narrows and
     the first cell repeats their data, so nothing needs scrolling sideways; the horizontal
     scroll is only the safety net.

     `stack="md"`: below that breakpoint each row is a card and each cell shows its `label`
     instead of hiding; with `expandable`, the cells with `hide` fold behind a button.
     `rows="separated"`: each row a card of its own, apart from the next.

     <x-wiretable>
         <x-slot:head>
             <x-wiretable.th>Event</x-wiretable.th>
             <x-wiretable.th hide="md" shrink>Date</x-wiretable.th>
         </x-slot:head>
         <x-wiretable.row wire:key="…"> <x-wiretable.td>…</x-wiretable.td> … </x-wiretable.row>
         <x-slot:footer>{{ $items->links() }}</x-slot:footer>
     </x-wiretable> --}}
<div {{ $attributes->class([
    'wtb-table',
    'wtb-stack-'.$stack => in_array($stack, ['sm', 'md', 'lg', 'xl'], true),
    'wtb-separated' => $rows === 'separated',
]) }}>
    <table class="wtb-grid">
        <thead class="wtb-head">
            <tr>{{ $head }}</tr>
        </thead>
        <tbody class="wtb-body">
            {{ $slot }}
        </tbody>
    </table>
@isset($footer)
    <div class="wtb-footer">{{ $footer }}</div>
@endisset
</div>
