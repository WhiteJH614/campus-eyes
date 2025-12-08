<?php

namespace App\Services\Sorting;

use Illuminate\Support\Collection;

/**
 * Sorts reports by due date (earliest first).
 */
class SortByDueDateStrategy implements SortStrategy
{
    public function sortReports(Collection|array $reports): Collection
    {
        $collection = $reports instanceof Collection ? $reports : collect($reports);

        return $collection->sortBy(function ($report) {
            $due = $report->due_date ?? $report['due_date'] ?? null;
            return $due ? strtotime($due) : PHP_INT_MAX;
        })->values();
    }
}
