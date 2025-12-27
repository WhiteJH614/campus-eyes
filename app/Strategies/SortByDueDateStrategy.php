<?php

// Author: Lee Jia Hui

namespace App\Strategies;

use Illuminate\Support\Collection;

class SortByDueDateStrategy implements SortStrategy
{
    public function sort(Collection $reports): Collection
    {
        return $reports
            ->sortBy(fn($r) => $r->due_at ?? '9999-12-31')
            ->values();
    }
}
