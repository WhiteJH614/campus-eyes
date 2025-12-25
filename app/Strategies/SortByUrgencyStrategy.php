<?php

// Author: Lee Jia Hui

namespace App\Strategies;

use Illuminate\Support\Collection;

class SortByUrgencyStrategy implements SortStrategy
{
    public function sort(Collection $reports): Collection
    {
        $rank = [
            'High' => 3,
            'Medium' => 2,
            'Low' => 1,
        ];

        return $reports
            ->sortByDesc(fn($r) => $rank[$r->urgency] ?? 0)
            ->values();
    }
}
