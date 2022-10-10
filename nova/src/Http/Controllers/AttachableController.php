<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\PivotableField;
use Laravel\Nova\Http\Requests\NovaRequest;

class AttachableController extends Controller
{
    /**
     * List the available related resources for a given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function __invoke(NovaRequest $request)
    {
        $field = $request->newResource()
                    ->availableFields($request)
                    ->filterForManyToManyRelations()
                    ->filter(function ($field) use ($request) {
                        return $field->resourceName === $request->field &&
                                    $field->attribute === $request->viaRelationship;
                    })->first();

        $withTrashed = $this->shouldIncludeTrashed(
            $request, $associatedResource = $field->resourceClass
        );

        $parentResource = $request->findResourceOrFail();

        return [
            'resources' => $field->buildAttachableQuery($request, $withTrashed)
                        ->tap($this->getAttachableQueryResolver($request, $field))
                        ->get()
                        ->mapInto($field->resourceClass)
                        ->filter(function ($resource) use ($request, $parentResource) {
                            return $parentResource->authorizedToAttach($request, $resource->resource);
                        })
                        ->map(function ($resource) use ($request, $field) {
                            return $field->formatAttachableResource($request, $resource);
                        })->sortBy('display', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'withTrashed' => $withTrashed,
            'softDeletes' => $associatedResource::softDeletes(),
        ];
    }

    /**
     * Determine if the query should include trashed models.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $associatedResource
     * @return bool
     */
    protected function shouldIncludeTrashed(NovaRequest $request, $associatedResource)
    {
        if ($request->withTrashed === 'true') {
            return true;
        }

        $associatedModel = $associatedResource::newModel();

        if ($request->current && $associatedResource::softDeletes()) {
            $associatedModel = $associatedModel->newQueryWithoutScopes()->find($request->current);

            return $associatedModel ? $associatedModel->trashed() : false;
        }

        return false;
    }

    /**
     * Get attachable query resolver.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Contracts\PivotableField  $field
     * @return callable(\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder):void
     */
    protected function getAttachableQueryResolver(NovaRequest $request, PivotableField $field)
    {
        return function ($query) use ($request, $field) {
            if (
                $request->first === 'true'
                || $field->allowDuplicateRelations
                || is_null($relatedModel = $request->findModel())
            ) {
                return;
            }

            $query->whereNotExists(function ($query) use ($field, $relatedModel) {
                $relation = $relatedModel->{$field->manyToManyRelationship}();

                return $relation->applyDefaultPivotQuery($query)
                        ->select($relation->getRelatedPivotKeyName())
                        ->whereColumn($relation->getQualifiedRelatedKeyName(), $relation->getQualifiedRelatedPivotKeyName());
            });
        };
    }
}
