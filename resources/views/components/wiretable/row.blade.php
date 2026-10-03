@aware(['expandable' => false])
<tr {{ $attributes->class('wtb-row') }}@if ($expandable) x-data="{ open: false }" x-bind:class="open && 'wtb-open'"@endif>{{ $slot }}</tr>
