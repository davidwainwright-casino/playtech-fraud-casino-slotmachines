<?php

namespace Laravel\Nova;

use Laravel\Nova\Query\ApplyFilter;

class FilterDecoder
{
    /**
     * The filter string to be decoded.
     *
     * @var string
     */
    protected $filterString;

    /**
     * The filters available via the request.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $availableFilters;

    /**
     * Create a new FilterDecoder instance.
     *
     * @param  string  $filterString
     * @param  \Illuminate\Support\Collection|array|null  $availableFilters
     */
    public function __construct($filterString, $availableFilters = null)
    {
        $this->filterString = $filterString;
        $this->availableFilters = collect($availableFilters);
    }

    /**
     * Decode the given filters.
     *
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Query\ApplyFilter>
     */
    public function filters()
    {
        if (empty($filters = $this->decodeFromBase64String())) {
            return collect();
        }

        return collect($filters)->map(function ($filter) {
            $class = key($filter);
            $value = $filter[$class];

            $matchingFilter = $this->availableFilters->first(function ($availableFilter) use ($class) {
                return $class === $availableFilter->key();
            });

            if ($matchingFilter) {
                return ['filter' => $matchingFilter, 'value' => $value];
            }
        })
            ->filter()
            ->reject(function ($filter) {
                if (is_array($filter['value'])) {
                    return count($filter['value']) < 1;
                } elseif (is_string($filter['value'])) {
                    return trim($filter['value']) === '';
                }

                return is_null($filter['value']);
            })->map(function ($filter) {
                return new ApplyFilter($filter['filter'], $filter['value']);
            })->values();
    }

    /**
     * Decode the filter string from base64 encoding.
     *
     * @return array<int, array<class-string<\Laravel\Nova\Filters\Filter>|string, mixed>>
     */
    public function decodeFromBase64String()
    {
        if (empty($this->filterString)) {
            return [];
        }

        $filters = json_decode(base64_decode($this->filterString), true);

        return is_array($filters) ? $filters : [];
    }
}
