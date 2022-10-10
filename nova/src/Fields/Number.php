<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Filters\NumberFilter;
use Laravel\Nova\Http\Requests\NovaRequest;

class Number extends Text
{
    /**
     * The minimum value that can be assigned to the field.
     *
     * @var mixed
     */
    public $min;

    /**
     * The maximum value that can be assigned to the field.
     *
     * @var mixed
     */
    public $max;

    /**
     * The step size the field will increment and decrement by.
     *
     * @var mixed
     */
    public $step;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|\Closure|callable|object|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->textAlign(Field::RIGHT_ALIGN)
            ->withMeta(['type' => 'number'])
            ->displayUsing(function ($value) {
                return ! $this->isNullValue($value) ? (string) $value : null;
            });
    }

    /**
     * The minimum value that can be assigned to the field.
     *
     * @param  mixed  $min
     * @return $this
     */
    public function min($min)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * The maximum value that can be assigned to the field.
     *
     * @param  mixed  $max
     * @return $this
     */
    public function max($max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * The step size the field will increment and decrement by.
     *
     * @param  mixed  $step
     * @return $this
     */
    public function step($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new NumberFilter($this);
    }

    /**
     * Define the default filterable callback.
     *
     * @return callable(\Laravel\Nova\Http\Requests\NovaRequest, \Illuminate\Database\Eloquent\Builder, mixed, string):\Illuminate\Database\Eloquent\Builder
     */
    protected function defaultFilterableCallback()
    {
        return function (NovaRequest $request, $query, $value, $attribute) {
            [$min, $max] = $value;

            if (! is_null($min) && ! is_null($max)) {
                return $query->whereBetween($attribute, [$min, $max]);
            } elseif (! is_null($min)) {
                return $query->where($attribute, '>=', $min);
            }

            return $query->where($attribute, '<=', $max);
        };
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
                'type',
                'min',
                'max',
                'step',
                'pattern',
                'placeholder',
                'extraAttributes',
            ]);
        });
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), collect([
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
        ])->reject(function ($value) {
            return is_null($value) || (empty($value) && $value !== 0);
        })->all());
    }
}
