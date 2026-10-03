<?php

namespace EduLazaro\Wiretables\Tests;

use Illuminate\Support\Facades\Blade;

class TableTest extends TestCase
{
    public function test_a_table_has_its_head_and_body(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-wiretable class="mt-4">
                <x-slot:head><x-wiretable.th>Event</x-wiretable.th></x-slot:head>
                <x-wiretable.row wire:key="r1"><x-wiretable.td>Gala</x-wiretable.td></x-wiretable.row>
            </x-wiretable>
            BLADE);

        $this->assertStringContainsString('class="wtb-table mt-4"', $html);
        $this->assertStringContainsString('<thead class="wtb-head">', $html);
        $this->assertMatchesRegularExpression('/<th class="wtb-th"\s*>Event<\/th>/', $html);
        $this->assertStringContainsString('<tr class="wtb-row" wire:key="r1">', $html);
        $this->assertStringContainsString('<td class="wtb-td">Gala</td>', $html);
    }

    public function test_columns_hide_below_their_breakpoint_and_shrink_to_their_content(): void
    {
        $th = Blade::render('<x-wiretable.th hide="md" shrink>Date</x-wiretable.th>');
        $td = Blade::render('<x-wiretable.td hide="lg" shrink>12</x-wiretable.td>');
        $odd = Blade::render('<x-wiretable.td hide="huge">x</x-wiretable.td>');

        $this->assertStringContainsString('class="wtb-th wtb-shrink wtb-from-md"', $th);
        $this->assertStringContainsString('class="wtb-td wtb-nowrap wtb-from-lg"', $td);
        $this->assertStringContainsString('class="wtb-td"', $odd);
    }

    public function test_the_actions_column_is_labelled_for_screen_readers_only(): void
    {
        app()->setLocale('es');

        $html = Blade::render('<x-wiretable.th actions />');

        $this->assertStringContainsString('class="wtb-th wtb-actions wtb-shrink"', $html);
        $this->assertStringContainsString('<span class="wtb-sr-only">Acciones</span>', $html);
    }

    public function test_a_sortable_header_says_its_order_and_calls_the_component(): void
    {
        $idle = Blade::render('<x-wiretable.th sortable="date" sort="" direction="">Date</x-wiretable.th>');
        $asc = Blade::render('<x-wiretable.th sortable="date" sort="date" direction="asc">Date</x-wiretable.th>');
        $desc = Blade::render('<x-wiretable.th sortable="date" sort="date" direction="desc" method="order">Date</x-wiretable.th>');

        $this->assertStringContainsString('aria-sort="none"', $idle);
        $this->assertStringContainsString('wire:click="sortBy(\'date\')" class="wtb-sort"', $idle);
        $this->assertStringContainsString('aria-sort="ascending"', $asc);
        $this->assertStringContainsString('class="wtb-sort wtb-sorted"', $asc);
        $this->assertStringContainsString('M4.5 10.5 12 3', $asc);
        $this->assertStringContainsString('aria-sort="descending"', $desc);
        $this->assertStringContainsString('wire:click="order(\'date\')"', $desc);
        $this->assertStringContainsString('M19.5 13.5 12 21', $desc);
    }

    public function test_the_first_cell_links_opens_in_place_or_just_names(): void
    {
        $link = Blade::render('<x-wiretable.primary title="Gala" subtitle="ana@acme.test" href="/p/1">On 8 Oct</x-wiretable.primary>');
        $button = Blade::render('<x-wiretable.primary title="Gala" action="open(1)" />');
        $text = Blade::render('<x-wiretable.primary title="Gala"><x-slot:leading><img alt=""></x-slot:leading></x-wiretable.primary>');

        $this->assertStringContainsString('<a href="/p/1" class="wtb-title wtb-title-link">Gala</a>', $link);
        $this->assertStringContainsString('<p class="wtb-subtitle">ana@acme.test</p>', $link);
        $this->assertStringContainsString('On 8 Oct', $link);
        $this->assertStringContainsString('x-on:click="open(1)" class="wtb-title wtb-title-button"', $button);
        $this->assertStringContainsString('<p class="wtb-title wtb-title-text">Gala</p>', $text);
        $this->assertStringContainsString('<img alt="">', $text);
    }

    public function test_an_empty_list_spans_every_column(): void
    {
        $html = Blade::render('<x-wiretable.empty colspan="5">Nothing yet.</x-wiretable.empty>');

        $this->assertStringContainsString('<td colspan="5" class="wtb-empty">Nothing yet.</td>', $html);
    }

    public function test_the_row_menu_moves_to_body_anchored_to_its_button(): void
    {
        $html = Blade::render('<x-wiretable.menu label="Actions of Gala"><a href="/p/1">Open</a></x-wiretable.menu>');

        $this->assertStringContainsString('aria-label="Actions of Gala"', $html);
        $this->assertStringContainsString('x-teleport="body"', $html);
        $this->assertStringContainsString('x-anchor.bottom-end.offset.4="$refs.trigger"', $html);
        $this->assertStringContainsString('class="wtb-menu-panel"', $html);
        $this->assertStringContainsString('<a href="/p/1">Open</a>', $html);
    }

    public function test_menu_items_are_links_or_buttons_and_can_be_dangerous(): void
    {
        $link = Blade::render("<x-wiretable.menu-item href=\"/p/1\">\n<x-slot:leading><svg></svg></x-slot:leading>\nOpen\n</x-wiretable.menu-item>");
        $button = Blade::render('<x-wiretable.menu-item wire:click="remove(1)" danger>Delete…</x-wiretable.menu-item>');
        $separator = Blade::render('<x-wiretable.menu-separator />');

        $this->assertMatchesRegularExpression('/<a\s+href="\/p\/1"\s+class="wtb-menu-item">\s*<svg><\/svg>\s*Open\s*<\/a>/', $link);
        $this->assertStringContainsString('type="button"', $button);
        $this->assertStringContainsString('class="wtb-menu-item wtb-danger"', $button);
        $this->assertStringContainsString('wire:click="remove(1)"', $button);
        $this->assertStringContainsString('<div role="separator" class="wtb-menu-separator"></div>', $separator);
    }
}
