<?php

namespace Laravel\Nova\Fields\Filters;

use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Filters\Filter as BaseFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

abstract class Filter extends BaseFilter
{
    /**
     * The filter's field.
     *
     * @var \Laravel\Nova\Contracts\FilterableField
     */
    public $field;

    /**
     * Construct a new filter.
     *
     * @param  \Laravel\Nova\Contracts\FilterableField  $field
     */
    public function __construct(FilterableField $field)
    {
        $this->field = $field;
    }

    /**
     * Get the displayable name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return $this->field->name;
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function key()
    {
        return class_basename($this->field).':'.$this->field->attribute;
    }

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
        $this->field->applyFilter($request, $query, $value);

        return $query;
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

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'component' => 'filter-'.$this->component,
            'field' => $this->serializeField(),
        ]);
    }
}
