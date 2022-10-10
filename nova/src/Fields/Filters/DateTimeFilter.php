<?php

namespace Laravel\Nova\Fields\Filters;

use Carbon\CarbonImmutable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class DateTimeFilter extends DateFilter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(NovaRequest $request, $query, $value)
    {
        $appTimezone = config('app.timezone');
        $userTimezone = Nova::resolveUserTimezone($request) ?? $appTimezone;

        $value = collect($value)->transform(function ($value) use ($userTimezone) {
            return ! empty($value) ? rescue(function () use ($value, $userTimezone) {
                return CarbonImmutable::createFromFormat('Y-m-d', $value, $userTimezone);
            }, null) : null;
        });

        if ($value->filter()->isNotEmpty()) {
            if ($value[0] instanceof CarbonImmutable) {
                $value[0] = $value[0]->startOfDay()->timezone($appTimezone);
            }

            if ($value[1] instanceof CarbonImmutable) {
                $value[1] = $value[1]->endOfDay()->timezone($appTimezone);
            }

            $this->field->applyFilter($request, $query, $value->all());
        }

        return $query;
    }
}
