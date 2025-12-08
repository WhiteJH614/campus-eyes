<?php

namespace App\Services\Sorting;

use Illuminate\Support\Collection;

/**
 * Sorts reports by urgency (High > Medium > Low > others).
 */
class SortByUrgencyStrategy implements SortStrategy
{
    protected array $priority = [
        'high' => 1,
        'medium' => 2,
        'low' => 3,
    ];

    public function sortReports(Collection|array $reports): Collection
    {
        $collection = $reports instanceof Collection ? $reports : collect($reports);

        return $collection->sortBy(function ($report) {
            $value = strtolower($report->urgency ?? $report['urgency'] ?? '');
            return $this->priority[$value] ?? 99;
        })->values();
    }
}
