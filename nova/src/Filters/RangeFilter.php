<?php

namespace Laravel\Nova\Filters;

abstract class RangeFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'range-filter';
}
