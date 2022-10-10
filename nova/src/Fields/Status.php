<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Filters\StatusFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class Status extends Text
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'status-field';

    /**
     * Indicates if the element should be shown on the creation view.
     *
     * @var bool
     */
    public $showOnCreation = false;

    /**
     * Indicates if the element should be shown on the update view.
     *
     * @var bool
     */
    public $showOnUpdate = false;

    /**
     * Indicate if field require explicit filterable callback.
     *
     * @var bool
     */
    public $requiresExplicitFilterableCallback = true;

    /**
     * Specify the values that should be considered "loading".
     *
     * @param  array<int, string>  $loadingWords
     * @return $this
     */
    public function loadingWhen(array $loadingWords)
    {
        return $this->withMeta(['loadingWords' => $loadingWords]);
    }

    /**
     * Specify the values that should be considered "failed".
     *
     * @param  array<int, string>  $failedWords
     * @return $this
     */
    public function failedWhen(array $failedWords)
    {
        return $this->withMeta(['failedWords' => $failedWords]);
    }

    /**
     * Resolve the field's status type.
     *
     * @return string
     */
    protected function resolveStatusType()
    {
        if (in_array($this->value, $this->meta['loadingWords'])) {
            return 'loading';
        }

        if (in_array($this->value, $this->meta['failedWords'])) {
            return 'failed';
        }

        return 'success';
    }

    /**
     * Resolve the field's status CSS class.
     *
     * @return string
     */
    protected function resolveTypeClass()
    {
        switch ($this->resolveStatusType()) {
            case 'loading':
                return 'bg-gray-100 text-gray-500 dark:bg-gray-900 dark:text-gray-400';
            case 'failed':
                return 'bg-red-100 text-red-600 dark:bg-red-400 dark:text-red-900';
            default:
                return 'bg-green-100 text-green-600 dark:bg-green-400 dark:text-green-900';
        }
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge([
            'type' => $this->resolveStatusType(),
            'typeClass' => $this->resolveTypeClass(),
        ], parent::jsonSerialize());
    }

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new StatusFilter($this);
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeForFilter()
    {
        return transform($this->jsonSerialize(), function ($field) {
            return Arr::only($field, [
                'uniqueKey',
                'name',
                'attribute',
                'options',
            ]);
        });
    }
}
