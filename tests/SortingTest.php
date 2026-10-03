<?php

namespace EduLazaro\Wiretables\Tests;

use EduLazaro\Wiretables\Concerns\WithSorting;
use Livewire\Component;
use Livewire\Livewire;
use Livewire\WithPagination;

class SortingTest extends TestCase
{
    public function test_a_header_cycles_ascending_descending_and_the_lists_own_order(): void
    {
        $table = Livewire::test(SortedList::class)->set('paginators.page', 3);

        $table->call('sortBy', 'date')->assertSet('sort', 'date')->assertSet('direction', 'asc')->assertSet('paginators.page', 1);
        $table->call('sortBy', 'date')->assertSet('sort', 'date')->assertSet('direction', 'desc');
        $table->call('sortBy', 'date')->assertSet('sort', '')->assertSet('direction', '');
        $table->call('sortBy', 'date')->call('sortBy', 'name')->assertSet('sort', 'name')->assertSet('direction', 'asc');
    }

    public function test_only_the_declared_keys_sort(): void
    {
        Livewire::test(SortedList::class)
            ->call('sortBy', 'password')
            ->assertSet('sort', '')
            ->assertSet('direction', '');
    }

    public function test_the_order_lives_in_the_url(): void
    {
        Livewire::withQueryParams(['sort' => 'name', 'direction' => 'desc'])
            ->test(SortedList::class)
            ->assertSet('sort', 'name')
            ->assertSet('direction', 'desc');
    }
}

class SortedList extends Component
{
    use WithPagination;
    use WithSorting;

    /**
     * @return list<string>
     */
    protected function sortable(): array
    {
        return ['date', 'name'];
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return '<div></div>';
    }
}
