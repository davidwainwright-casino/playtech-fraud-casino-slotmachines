<?php

namespace Laravel\Nova\Fields\Filters;

use Laravel\Nova\Http\Requests\NovaRequest;

class StatusFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Get the filter's available options.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function options(NovaRequest $request)
    {
        return [
            'loading' => __('Loading'),
            'finished' => __('Finished'),
            'failed' => __('Failed'),
        ];
    }

    /**
     * Prepare the filter for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'component' => $this->component,
            'field' => $this->serializeField(),
        ]);
    }
}
