<?php

// Author: Lee Jia Hui

namespace App\Services;

use App\Strategies\SortStrategy;
use Illuminate\Support\Collection;

class TechnicianJobView
{
    private SortStrategy $strategy;

    public function __construct(SortStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function setStrategy(SortStrategy $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function getSortedReports(Collection $reports): Collection
    {
        return $this->strategy->sort($reports);
    }
}
