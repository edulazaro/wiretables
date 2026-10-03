<?php

namespace EduLazaro\Wiretables\Tests;

use EduLazaro\Wiretables\Concerns\WithLoadMore;
use EduLazaro\Wiretables\Concerns\WithSorting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\Livewire;

class LoadMoreTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('items', function ($table) {
            $table->id();
            $table->string('name');
        });
        DB::table('items')->insert(array_map(fn ($i) => ['name' => "item {$i}"], range(1, 7)));
    }

    public function test_each_load_adds_a_page_until_there_is_no_more(): void
    {
        $list = Livewire::test(GrowingList::class);

        $list->assertSet('pages', 1)->assertSet('hasMore', true)->assertSee('count:3');
        $list->call('loadMore')->assertSet('pages', 2)->assertSee('count:6')->assertSet('hasMore', true);
        $list->call('loadMore')->assertSee('count:7')->assertSet('hasMore', false);
        $list->call('loadMore')->assertSet('pages', 3);
    }

    public function test_a_filter_or_a_new_order_starts_again(): void
    {
        Livewire::test(GrowingList::class)
            ->call('loadMore')->assertSet('pages', 2)
            ->set('q', 'x')->assertSet('pages', 1)
            ->call('loadMore')->call('sortBy', 'id')->assertSet('pages', 1);
    }

    public function test_the_client_cannot_choose_how_many_pages(): void
    {
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);

        Livewire::test(GrowingList::class)->set('pages', 1000);
    }
}

class GrowingList extends Component
{
    use WithLoadMore;
    use WithSorting;

    public string $q = '';

    /**
     * @return int
     */
    protected function perLoad(): int
    {
        return 3;
    }

    /**
     * @return list<string>
     */
    protected function sortable(): array
    {
        return ['id'];
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $items = $this->loadMoreFrom(DB::table('items')->orderBy('id'));

        return '<div>count:'.$items->count().'</div>';
    }
}
