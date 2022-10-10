<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Contracts\Deletable;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Nova;

trait DetachesPivotModels
{
    /**
     * Get the pivot record detachment callback for the field.
     *
     * @return \Closure(\Laravel\Nova\Http\Requests\NovaRequest, mixed):bool
     */
    protected function detachmentCallback()
    {
        return function ($request, $model) {
            $pivotAccessor = $model->{$this->attribute}()->getPivotAccessor();

            foreach ($model->{$this->attribute}()->withoutGlobalScopes()->cursor() as $related) {
                $resource = Nova::newResourceFromModel($related);

                $pivot = $related->{$pivotAccessor};

                $pivotFields = $resource->resolvePivotFields($request, $request->resource);

                $pivotFields->whereInstanceOf(Deletable::class)
                        ->filter->isPrunable()
                        ->each(function ($field) use ($request, $pivot) {
                            DeleteField::forRequest($request, $field, $pivot)->save();
                        });

                $pivot->delete();
            }

            return true;
        };
    }
}
