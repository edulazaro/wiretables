<?php

namespace EduLazaro\Wiretables\Concerns;

use Livewire\Attributes\Url;

/**
 * Header sorting for a Livewire component that draws an `x-wiretable`.
 *
 * A sortable header (`<x-wiretable.th sortable="date" :sort="$sort" :direction="$direction">`)
 * calls `sortBy('date')`, which cycles ascending, descending, and back to the list's own
 * order (`sort` ''), and goes back to the first page. Only the keys `sortable()` returns are
 * accepted: the client can send any string, and a column name must never come from it. The
 * order lives in the URL, so a sorted list can be reloaded or shared.
 *
 * The query reads `$this->sort` and `$this->direction` and maps the key to a column itself.
 */
trait WithSorting
{
    /** The key the list is ordered by, '' for the list's own order. */
    #[Url]
    public string $sort = '';

    /** 'asc' or 'desc' while `sort` is set, '' otherwise. */
    #[Url]
    public string $direction = '';

    /**
     * The keys a header may sort by.
     *
     * @return list<string>
     */
    protected function sortable(): array
    {
        return [];
    }

    /**
     * @param  string  $column
     * @return void
     */
    public function sortBy(string $column): void
    {
        if (! in_array($column, $this->sortable(), true)) {
            return;
        }

        [$this->sort, $this->direction] = match (true) {
            $this->sort !== $column => [$column, 'asc'],
            $this->direction === 'asc' => [$column, 'desc'],
            default => ['', ''],
        };

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }
}
