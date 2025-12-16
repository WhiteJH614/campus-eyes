<?php

namespace App\Factories;

use App\Strategies\SortStrategy;
use App\Strategies\SortByUrgencyStrategy;
use App\Strategies\SortByDueDateStrategy;
use App\Strategies\SortByBlockStrategy;

class SortStrategyFactory
{
    public static function make(?string $key): SortStrategy
    {
        return match ($key) {
            'urgency' => new SortByUrgencyStrategy(),
            'due' => new SortByDueDateStrategy(),
            'block' => new SortByBlockStrategy(),
            default => new SortByDueDateStrategy(),
        };
    }
}
