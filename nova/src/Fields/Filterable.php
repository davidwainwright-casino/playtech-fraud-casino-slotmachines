<?php

namespace Laravel\Nova\Fields;

use InvalidArgumentException;
use Laravel\Nova\Http\Requests\NovaRequest;

trait Filterable
{
    /**
     * The callback used to determine if the field is filterable.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Database\Eloquent\Builder, mixed, string):void)|null
     */
    public $filterableCallback;

    /**
     * The callback used to determine if the field is filterable.
     *
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Database\Eloquent\Builder, mixed, string):void)|null  $filterableCallback
     * @return $this
     */
    public function filterable(callable $filterableCallback = null)
    {
        if (property_exists($this, 'requiresExplicitFilterableCallback')
            && $this->requiresExplicitFilterableCallback === true
            && is_null($filterableCallback)
        ) {
            throw new InvalidArgumentException('$filterableCallback needs to be callable/Closure');
        }

        $this->filterableCallback = ! is_null($filterableCallback)
                                        ? $filterableCallback
                                        : $this->defaultFilterableCallback();

        return $this;
    }

    /**
     * Set field as without filterable.
     *
     * @return $this
     */
    public function withoutFilterable()
    {
        $this->filterableCallback = null;

        return $this;
    }

    /**
     * Apply the filter to the given query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return void
     */
    public function applyFilter(NovaRequest $request, $query, $value)
    {
        call_user_func($this->filterableCallback, $request, $query, $value, $this->filterableAttribute($request));
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeForFilter()
    {
        return $this->jsonSerialize();
    }

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter|null
     */
    public function resolveFilter(NovaRequest $request)
    {
        return is_callable($this->filterableCallback) ? $this->makeFilter($request) : null;
    }

    /**
     * Define the default filterable callback.
     *
     * @return callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Database\Eloquent\Builder, mixed):\Illuminate\Database\Eloquent\Builder
     */
    protected function defaultFilterableCallback()
    {
        return function (NovaRequest $request, $query, $value) {
            return $query->where($this->filterableAttribute($request), '=', $value);
        };
    }

    /**
     * Define filterable attribute.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    protected function filterableAttribute(NovaRequest $request)
    {
        return $this->attribute;
    }

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter|null
     */
    abstract protected function makeFilter(NovaRequest $request);
}
