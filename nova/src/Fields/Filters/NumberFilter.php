<?php

namespace Laravel\Nova\Fields\Filters;

use Laravel\Nova\Http\Requests\NovaRequest;

class NumberFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'number-field';

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
        $value = collect($value)->transform(function ($value) {
            return ! empty($value) ? $value : null;
        });

        if ($value->filter()->isNotEmpty()) {
            $this->field->applyFilter($request, $query, $value->all());
        }

        return $query;
    }

    /**
     * Get the default options for the filter.
     *
     * @return array|mixed
     */
    public function default()
    {
        return [null, null];
    }
}
