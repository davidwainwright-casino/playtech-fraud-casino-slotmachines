<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * @property array $fieldDependencies
 */
trait DependentFields
{
    /**
     * Resolve the dependent component key.
     *
     * @return string
     */
    public function dependentComponentKey()
    {
        return sprintf('%s.%s.%s', Str::slug(class_basename(get_called_class())), $this->component, $this->attribute);
    }

    /**
     * Resolve dependent field value.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function resolveDependentValue(NovaRequest $request)
    {
        return $this->value ?? $this->resolveDefaultValue($request);
    }

    /**
     * Sync depends on logic.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return $this
     */
    public function syncDependsOn(NovaRequest $request)
    {
        $this->value = null;
        $this->defaultCallback = null;

        return $this->applyDependsOn($request);
    }

    /**
     * Apply depends on logic.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return $this
     */
    public function applyDependsOn(NovaRequest $request)
    {
        $this->fieldDependencies = collect($this->fieldDependencies ?? [])
            ->map(function ($dependsOn) use ($request) {
                $mixin = $dependsOn['mixin'];

                if (is_string($mixin) && class_exists($mixin)) {
                    $mixin = new $mixin();
                }

                return [
                    'mixin' => $mixin,
                    'attributes' => $dependsOn['attributes'],
                    'formData' => FormData::onlyFrom($request, $dependsOn['attributes']),
                ];
            })
            ->each(function ($dependsOn) use ($request) {
                $dependsOn['mixin'](
                    $this, $request, $dependsOn['formData']
                );
            })->all();

        return $this;
    }

    /**
     * Get depends on attributes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<string, mixed>|null
     */
    protected function getDependentsAttributes(NovaRequest $request)
    {
        return collect($this->fieldDependencies ?? [])->map(function ($dependsOn) {
            return collect($dependsOn['attributes'])->mapWithKeys(function ($attribute) use ($dependsOn) {
                return [$attribute => optional(Arr::get($dependsOn, 'formData'))->get($attribute)];
            })->all();
        })->first() ?? null;
    }
}
