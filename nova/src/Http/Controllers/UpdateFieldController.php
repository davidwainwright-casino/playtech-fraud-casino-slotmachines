<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest;
use Laravel\Nova\Http\Resources\UpdateViewResource;

class UpdateFieldController extends Controller
{
    /**
     * List the update fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(ResourceUpdateOrUpdateAttachedRequest $request)
    {
        return UpdateViewResource::make()->toResponse($request);
    }

    /**
     * Synchronize the field for updating.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(ResourceUpdateOrUpdateAttachedRequest $request)
    {
        UpdateViewResource::make()->newResourceWith($request);

        return response()->json(
            $request->newResource()
                ->updateFields($request)
                ->filter(function ($field) use ($request) {
                    return $request->query('field') === $field->attribute &&
                            $request->query('component') === $field->dependentComponentKey();
                })->each->syncDependsOn($request)
                ->first()
        );
    }
}
