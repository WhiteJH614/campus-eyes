<?php

namespace App\Strategies;

use Illuminate\Support\Collection;

interface SortStrategy
{
    /**
     * Sort a collection of reports.
     *
     * @param Collection $reports
     * @return Collection
     */
    public function sort(Collection $reports): Collection;
}
