<?php

namespace Laravel\Nova;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class ResourceCollection extends Collection
{
    /**
     * Return the authorized resources of the collection.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function authorized(Request $request)
    {
        return $this->filter(function ($resource) use ($request) {
            return $resource::authorizedToViewAny($request);
        });
    }

    /**
     * Return the resources available to be displayed in the navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function availableForNavigation(Request $request)
    {
        return $this->filter(function ($resource) use ($request) {
            return $resource::availableForNavigation($request);
        });
    }

    /**
     * Return the searchable resources for the collection.
     *
     * @return static
     */
    public function searchable()
    {
        return $this->filter(function ($resource) {
            return $resource::$globallySearchable;
        });
    }

    /**
     * Sort the resources by their group property.
     *
     * @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<array-key, TValue>>
     */
    public function grouped()
    {
        return $this->groupBy(function ($resource, $key) {
            return $resource::group();
        })->toBase()->sortKeys();
    }

    /**
     * Group the resources for display in navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<array-key, TValue>>
     */
    public function groupedForNavigation(Request $request)
    {
        return $this->availableForNavigation($request)->grouped();
    }
}
