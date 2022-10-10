<?php

namespace Laravel\Nova\Http\Resources;

use Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest;

class UpdateViewResource extends Resource
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
            'title' => (string) $resource->title(),
            'fields' => $fields = $resource->updateFieldsWithinPanels($request, $resource)->applyDependsOnWithDefaultValues($request),
            'panels' => $resource->availablePanelsForUpdate($request, $resource, $fields),
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
        return tap($request->newResourceWith(
            tap($request->findModelQuery(), function ($query) use ($request) {
                $resource = $request->resource();
                $resource::editQuery($request, $query);
            })->firstOrFail()
        ))->authorizeToUpdate($request);
    }
}
