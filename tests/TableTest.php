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

    public function test_a_truncating_column_cuts_its_text_instead_of_growing(): void
    {
        $th = Blade::render('<x-wiretable.th truncate>Address</x-wiretable.th>');
        $td = Blade::render('<x-wiretable.td truncate>A very long address indeed</x-wiretable.td>');

        // It goes on both: in a table the widest cell of a column decides the column's width,
        // so capping only one of the two leaves the other free to grow.
        $this->assertStringContainsString('class="wtb-th wtb-truncate"', $th);
        $this->assertStringContainsString('class="wtb-td wtb-truncate"', $td);

        // `max-width: 0` is what makes it work: without it the cell grows to fit its text and
        // the overflow rules never come into play.
        $css = file_get_contents(__DIR__.'/../resources/css/wiretables-core.css');

        $this->assertMatchesRegularExpression('/\.wtb-truncate\s*\{[^}]*max-width:\s*0/', $css);
        $this->assertMatchesRegularExpression('/\.wtb-truncate\s*\{[^}]*text-overflow:\s*ellipsis/', $css);

        // And it undoes itself in a stacked card, where there is room and the text is read
        // whole: a cell left at `max-width: 0` while `display: block` would show nothing.
        foreach (['sm', 'md', 'lg', 'xl'] as $breakpoint) {
            $this->assertMatchesRegularExpression(
                '/\.wtb-stack-'.$breakpoint.' \.wtb-row > \.wtb-td \{[^}]*max-width:\s*none/',
                $css,
                "The {$breakpoint} card does not undo the truncation.",
            );
        }
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

    public function test_the_menu_can_put_its_items_on_the_right(): void
    {
        $left = Blade::render('<x-wiretable.menu label="Actions"><a href="/p/1">Open</a></x-wiretable.menu>');
        $right = Blade::render('<x-wiretable.menu label="Actions" align="right"><a href="/p/1">Open</a></x-wiretable.menu>');

        $this->assertStringContainsString('class="wtb-menu-panel"', $left);
        $this->assertStringContainsString('class="wtb-menu-panel wtb-menu-right"', $right);
        $this->assertStringContainsString('.wtb-menu-right .wtb-menu-item', file_get_contents(__DIR__.'/../resources/css/wiretables-core.css'));
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

    public function test_figures_align_right_with_the_arrow_inside(): void
    {
        $th = Blade::render('<x-wiretable.th align="right" sortable="total" sort="" direction="">Total</x-wiretable.th>');
        $td = Blade::render('<x-wiretable.td align="right" shrink>1.200 €</x-wiretable.td>');

        $this->assertStringContainsString('class="wtb-th wtb-right"', $th);
        $this->assertStringContainsString('class="wtb-td wtb-nowrap wtb-right"', $td);
    }

    public function test_the_name_can_navigate_without_a_reload(): void
    {
        $html = Blade::render('<x-wiretable.primary title="Gala" href="/p/1" navigate />');

        $this->assertStringContainsString('wire:navigate', $html);
        $this->assertStringNotContainsString('wire:navigate', Blade::render('<x-wiretable.primary title="Gala" href="/p/1" />'));
    }

    public function test_a_footer_sits_under_the_rows_only_when_given(): void
    {
        $with = Blade::render("<x-wiretable>\n<x-slot:head><x-wiretable.th>A</x-wiretable.th></x-slot:head>\n<x-slot:footer>\nPages\n</x-slot:footer>\n</x-wiretable>");
        $without = Blade::render("<x-wiretable>\n<x-slot:head><x-wiretable.th>A</x-wiretable.th></x-slot:head>\n</x-wiretable>");

        $this->assertMatchesRegularExpression('/<div class="wtb-footer">\s*Pages\s*<\/div>/', $with);
        $this->assertStringNotContainsString('wtb-footer', $without);
    }

    public function test_load_more_shows_only_while_there_is_more(): void
    {
        app()->setLocale('es');

        $this->assertStringContainsString('wire:click="loadMore"', Blade::render('<x-wiretable.load-more :show="true" />'));
        $this->assertStringContainsString('Cargar más', Blade::render('<x-wiretable.load-more :show="true" />'));
        $this->assertStringNotContainsString('wtb-load-more', Blade::render('<x-wiretable.load-more :show="false" />'));
    }

    public function test_a_stacked_expandable_table_folds_its_rows(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-wiretable stack="md" expandable rows="separated">
                <x-slot:head><x-wiretable.th>Name</x-wiretable.th></x-slot:head>
                <x-wiretable.row>
                    <x-wiretable.td label="Name">Gala</x-wiretable.td>
                    <x-wiretable.td hide="md" label="Date">8 Oct</x-wiretable.td>
                    <x-wiretable.td actions>⋯</x-wiretable.td>
                </x-wiretable.row>
            </x-wiretable>
            BLADE);

        $this->assertStringContainsString('class="wtb-table wtb-stack-md wtb-separated"', $html);
        $this->assertStringContainsString('x-data="{ open: false }"', $html);
        $this->assertStringContainsString('data-label="Date"', $html);
        $this->assertStringContainsString('class="wtb-expand"', $html);

        $plain = Blade::render("<x-wiretable>\n<x-slot:head><x-wiretable.th>A</x-wiretable.th></x-slot:head>\n<x-wiretable.row><x-wiretable.td actions>⋯</x-wiretable.td></x-wiretable.row>\n</x-wiretable>");

        $this->assertStringNotContainsString('x-data', $plain);
        $this->assertStringNotContainsString('wtb-expand', $plain);
    }

    public function test_a_flush_or_compact_table_says_so(): void
    {
        $html = Blade::render("<x-wiretable flush stack=\"md\" compact>\n<x-slot:head><x-wiretable.th>A</x-wiretable.th></x-slot:head>\n</x-wiretable>");

        $this->assertStringContainsString('class="wtb-table wtb-stack-md wtb-compact wtb-flush"', $html);
    }

    public function test_a_row_can_open_a_page_or_run_an_action_without_stealing_inner_clicks(): void
    {
        $link = Blade::render('<x-wiretable.row href="/clients/7" navigate wire:key="r7"><x-wiretable.td>Ana</x-wiretable.td></x-wiretable.row>');
        $action = Blade::render('<x-wiretable.row action="$wire.edit(7)"><x-wiretable.td>Ana</x-wiretable.td></x-wiretable.row>');
        $plain = Blade::render('<x-wiretable.row><x-wiretable.td>Ana</x-wiretable.td></x-wiretable.row>');

        $this->assertStringContainsString('class="wtb-row wtb-row-link"', $link);
        $this->assertStringContainsString('tabindex="0"', $link);
        $this->assertStringContainsString('Livewire.navigate', $link);
        $this->assertStringContainsString("'\\/clients\\/7'", $link);
        $this->assertStringContainsString('wire:key="r7"', $link);

        // Whatever is interactive inside the row keeps its own click: the menu, a link, a field.
        $this->assertStringContainsString("closest('a, button, input, select, textarea, label, summary, [contenteditable], [data-wtb-ignore]')", $link);
        $this->assertStringContainsString('window.getSelection()', $link);
        $this->assertStringContainsString('$event.ctrlKey || $event.metaKey', $link);
        $this->assertStringContainsString('x-on:keydown.enter.self', $link);

        $this->assertStringContainsString('$wire.edit(7)', $action);
        $this->assertStringNotContainsString('window.location', $action);

        $this->assertStringContainsString('<tr class="wtb-row">', $plain);
        $this->assertStringNotContainsString('tabindex', $plain);
    }

    public function test_a_clickable_row_keeps_its_fold_when_expandable(): void
    {
        $html = Blade::render(<<<'BLADE'
            <x-wiretable stack="md" expandable>
                <x-slot:head><x-wiretable.th>Name</x-wiretable.th></x-slot:head>
                <x-wiretable.row href="/a"><x-wiretable.td>A</x-wiretable.td></x-wiretable.row>
            </x-wiretable>
            BLADE);

        $this->assertStringContainsString('x-data="{ open: false }"', $html);
        $this->assertStringContainsString("x-bind:class=\"open && 'wtb-open'\"", $html);
        $this->assertStringContainsString('wtb-row-link', $html);
    }
}
