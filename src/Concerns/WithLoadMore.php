<?php

namespace EduLazaro\Wiretables\Concerns;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;

/**
 * A list read in growing pages: "Load more" (`<x-wiretable.load-more :show="$hasMore" />`) adds
 * the next page under the ones already shown.
 *
 * Each read asks for everything up to the current page, from the start, instead of skipping
 * what was loaded: a record created or deleted meanwhile would otherwise shift the offset,
 * repeating or losing rows. The page count is locked: the client cannot ask for a thousand at
 * once. Any property update (a filter, the search) starts again from the first page, and so
 * does sorting (`WithSorting`).
 *
 *     $projects = $this->loadMoreFrom(Project::query()->latest());
 */
trait WithLoadMore
{
    /** How many pages are shown. */
    #[Locked]
    public int $pages = 1;

    /** Whether there is more after them. */
    #[Locked]
    public bool $hasMore = false;

    /**
     * Rows per page.
     *
     * @return int
     */
    protected function perLoad(): int
    {
        return 30;
    }

    /**
     * @return void
     */
    public function loadMore(): void
    {
        if ($this->hasMore) {
            $this->pages++;
        }
    }

    /**
     * @return void
     */
    public function resetLoadMore(): void
    {
        $this->pages = 1;
    }

    /**
     * Livewire calls it after any property update: a new filter shows its first page.
     *
     * @return void
     */
    public function updatedWithLoadMore(): void
    {
        $this->resetLoadMore();
    }

    /**
     * The rows of the pages shown, and whether there are more.
     *
     * @param  Builder  $query
     * @return Collection<int, mixed>
     */
    protected function loadMoreFrom(Builder $query): Collection
    {
        $limit = $this->pages * $this->perLoad();
        $rows = $query->limit($limit + 1)->get();

        $this->hasMore = $rows->count() > $limit;

        return $rows->take($limit);
    }
}
