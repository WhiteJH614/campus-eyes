<?php

namespace App\Services\Sorting;

use Illuminate\Support\Collection;

/**
 * Sorts reports by block name/code.
 */
class SortByBlockStrategy implements SortStrategy
{
    public function sortReports(Collection|array $reports): Collection
    {
        $collection = $reports instanceof Collection ? $reports : collect($reports);

        return $collection->sortBy(function ($report) {
            // Prefer a related block name, fall back to block code/id.
            $blockName = $report->block->block_name
                ?? $report->block_name
                ?? $report['block_name']
                ?? $report->block_code
                ?? $report['block_code']
                ?? $report->block_id
                ?? $report['block_id']
                ?? '';

            return strtolower((string) $blockName);
        })->values();
    }
}
