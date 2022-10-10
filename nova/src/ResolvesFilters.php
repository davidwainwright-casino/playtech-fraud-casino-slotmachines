<?php

namespace Laravel\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesFilters
{
    /**
     * Get the filters that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Filters\Filter>
     */
    public function availableFilters(NovaRequest $request)
    {
        return $this->resolveFilters($request)
                    ->concat($this->resolveFiltersFromFields($request))
                    ->filter->authorizedToSee($request)
                    ->values();
    }

    /**
     * Get the filters for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Filters\Filter>
     */
    public function resolveFilters(NovaRequest $request)
    {
        return collect(array_values($this->filter($this->filters($request))));
    }

    /**
     * Get the filters from filterable fields for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public function resolveFiltersFromFields(NovaRequest $request)
    {
        return collect(array_values($this->filter(
            $this->filterableFields($request)->transform(function ($field) use ($request) {
                return $field->resolveFilter($request);
            })->filter()->all()
        )));
    }

    /**
     * Get the filters available on the entity.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }
}
