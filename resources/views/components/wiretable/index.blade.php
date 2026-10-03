{{-- A table that reads the same everywhere: rows of the record first and the rest fitted to
     their content (`x-wiretable.th` / `x-wiretable.td`). Columns hide as the screen narrows and
     the first cell repeats their data, so nothing needs scrolling sideways; the horizontal
     scroll is only the safety net.

     <x-wiretable>
         <x-slot:head>
             <x-wiretable.th>Event</x-wiretable.th>
             <x-wiretable.th hide="md" shrink>Date</x-wiretable.th>
         </x-slot:head>
         <x-wiretable.row wire:key="…"> <x-wiretable.td>…</x-wiretable.td> … </x-wiretable.row>
     </x-wiretable> --}}
<div {{ $attributes->class('wtb-table') }}>
    <table class="wtb-grid">
        <thead class="wtb-head">
            <tr>{{ $head }}</tr>
        </thead>
        <tbody class="wtb-body">
            {{ $slot }}
        </tbody>
    </table>
</div>
