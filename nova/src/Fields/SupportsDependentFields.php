<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;

trait SupportsDependentFields
{
    /**
     * List of field dependencies.
     *
     * @var array<int, array{attributes: array<int, string>, mixin: callable|class-string}>
     */
    protected $fieldDependencies = [];

    /**
     * Register depends on to a field.
     *
     * @param  string|\Laravel\Nova\Fields\Field|array<int, string|\Laravel\Nova\Fields\Field>  $attributes
     * @param  (callable(static, \Laravel\Nova\Http\Requests\NovaRequest, \Laravel\Nova\Fields\FormData):void)|class-string  $mixin
     * @return $this
     */
    public function dependsOn($attributes, $mixin)
    {
        array_push($this->fieldDependencies, [
            'attributes' => collect(Arr::wrap($attributes))->map(function ($item) {
                if ($item instanceof MorphTo) {
                    return [$item->attribute, "{$item->attribute}_type"];
                }

                return $item instanceof Field ? $item->attribute : $item;
            })->flatten()->all(),
            'mixin' => $mixin,
        ]);

        return $this;
    }
}
