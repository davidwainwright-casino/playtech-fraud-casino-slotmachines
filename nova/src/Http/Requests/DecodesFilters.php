<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\FilterDecoder;

/**
 * @property-read string $filters
 */
trait DecodesFilters
{
    /**
     * Get the filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function filters()
    {
        return (new FilterDecoder($this->filters, $this->availableFilters()))->filters();
    }

    /**
     * Get all of the possibly available filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableFilters()
    {
        return $this->newResource()->availableFilters($this);
    }
}
