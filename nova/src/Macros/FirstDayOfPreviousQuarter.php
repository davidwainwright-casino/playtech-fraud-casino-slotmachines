<?php

namespace Laravel\Nova\Macros;

use Illuminate\Support\Carbon;

class FirstDayOfPreviousQuarter
{
    /**
     * Execute the macro.
     *
     * @return \DateTimeInterface
     */
    public function firstDayOfPreviousQuarter()
    {
        return function ($timezone = 'UTC') {
            return Carbon::now($timezone)->subQuarterWithoutOverflow()->startOfQuarter();
        };
    }
}
