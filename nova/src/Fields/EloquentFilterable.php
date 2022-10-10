<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Fields\Filters\EloquentFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

trait EloquentFilterable
{
    use Filterable;

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\EloquentFilter|null
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new EloquentFilter($this);
    }

    /**
     * Define filterable attribute.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    abstract protected function filterableAttribute(NovaRequest $request);

    /**
     * Define the default filterable callback.
     *
     * @return callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Database\Eloquent\Builder, mixed, string):void
     */
    abstract protected function defaultFilterableCallback();
}
