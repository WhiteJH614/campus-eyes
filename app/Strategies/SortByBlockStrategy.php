<?php

// Author: Lee Jia Hui

namespace App\Strategies;

use Illuminate\Support\Collection;

class SortByBlockStrategy implements SortStrategy
{
    public function sort(Collection $reports): Collection
    {
        return $reports
            ->sortBy(fn($r) => optional(optional($r->room)->block)->block_name ?? '')
            ->values();
    }
}
