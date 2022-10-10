<?php

namespace Laravel\Nova\Fields\Filters;

class MorphToFilter extends EloquentFilter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'morph-to-field';

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function key()
    {
        return 'resource:morphable:'.$this->field->attribute;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeField()
    {
        return $this->field->serializeForFilter();
    }
}
