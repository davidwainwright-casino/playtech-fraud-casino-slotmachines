<?php

namespace Laravel\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;

trait FillsFields
{
    /**
     * Fill a new model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    public static function fill(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->creationFields($request)->applyDependsOn($request)->withoutReadonly($request)
        );
    }

    /**
     * Fill a new model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    public static function fillForUpdate(NovaRequest $request, $model)
    {
        return static::fillFields(
            $request, $model,
            (new static($model))->updateFields($request)->applyDependsOn($request)->withoutReadonly($request)
        );
    }

    /**
     * Fill a new pivot model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Relations\Pivot  $pivot
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    public static function fillPivot(NovaRequest $request, $model, $pivot)
    {
        $instance = new static($model);

        return static::fillFields(
            $request, $pivot,
            $instance->creationPivotFields($request, $request->relatedResource)->applyDependsOn($request)
        );
    }

    /**
     * Fill a new pivot model instance using the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Relations\Pivot  $pivot
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    public static function fillPivotForUpdate(NovaRequest $request, $model, $pivot)
    {
        $instance = new static($model);

        return static::fillFields(
            $request, $pivot,
            $instance->updatePivotFields($request, $request->relatedResource)->applyDependsOn($request)
        );
    }

    /**
     * Fill the given fields for the model.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Support\Collection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return array{\Illuminate\Database\Eloquent\Model, array<int, callable>}
     */
    protected static function fillFields(NovaRequest $request, $model, $fields)
    {
        return [$model, $fields->map->fill($request, $model)->filter(function ($callback) {
            return is_callable($callback);
        })->values()->all()];
    }
}
