<?php

namespace App\Services;

use App\Services\Sorting\SortStrategy;
use Illuminate\Support\Collection;

class TechnicianView
{
    public function __construct(
        protected SortStrategy $strategy
    ) {
    }

    public function setStrategy(SortStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    /**
     * @param  \Illuminate\Support\Collection|array  $reports
     * @return \Illuminate\Support\Collection
     */
    public function getSortedReports(Collection|array $reports): Collection
    {
        return $this->strategy->sortReports($reports);
    }
}
