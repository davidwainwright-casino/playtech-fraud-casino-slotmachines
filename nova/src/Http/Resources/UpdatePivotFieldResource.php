<?php

namespace Laravel\Nova\Http\Resources;

use Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest;

class UpdatePivotFieldResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return array
     */
    public function toArray($request)
    {
        $resource = $this->newResourceWith($request);

        return [
            'title' => $resource->title(),
            'fields' => $resource->updatePivotFields(
                $request,
                $request->relatedResource
            )->applyDependsOnWithDefaultValues($request)->all(),
        ];
    }

    /**
     * Get current resource for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return \Laravel\Nova\Resource
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function newResourceWith(ResourceUpdateOrUpdateAttachedRequest $request)
    {
        $resource = $this->authorizedResourceForRequest($request);

        $model = $resource->model();

        $relation = $model->{$request->viaRelationship}();

        $accessor = $relation->getPivotAccessor();

        if ($request->viaPivotId) {
            tap($relation->getPivotClass(), function ($pivotClass) use ($relation, $request) {
                $relation->wherePivot((new $pivotClass())->getKeyName(), $request->viaPivotId);
            });
        }

        $model->setRelation(
            $accessor,
            $relation->withoutGlobalScopes()->findOrFail($request->relatedResourceId)->{$accessor}
        );

        return $resource;
    }

    /**
     * Get resource for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return \Laravel\Nova\Resource
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function authorizedResourceForRequest(ResourceUpdateOrUpdateAttachedRequest $request)
    {
        return tap($request->findResourceOrFail(), function ($resource) use ($request) {
            abort_unless($resource->hasRelatableField($request, $request->viaRelationship), 404);
        });
    }
}
