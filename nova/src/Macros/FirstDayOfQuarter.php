<?php

namespace Laravel\Nova\Macros;

use Illuminate\Support\Carbon;

class FirstDayOfQuarter
{
    /**
     * Execute the macro.
     *
     * @param  string  $timezone
     * @return \DateTimeInterface
     */
    public function firstDayOfQuarter()
    {
        return function ($timezone = 'UTC') {
            return Carbon::now($timezone)->startOfQuarter();
        };
    }
}
