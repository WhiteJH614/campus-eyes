<?php

namespace App\Services\Sorting;

use Illuminate\Support\Collection;

/**
 * Contract for report-sorting strategies.
 */
interface SortStrategy
{
    /**
     * @param  \Illuminate\Support\Collection|array  $reports
     * @return \Illuminate\Support\Collection
     */
    public function sortReports(Collection|array $reports): Collection;
}
