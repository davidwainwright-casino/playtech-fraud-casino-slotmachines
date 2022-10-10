<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;
use Laravel\Nova\Http\Resources\CreateViewResource;
use Laravel\Nova\Http\Resources\ReplicateViewResource;

class CreationFieldController extends Controller
{
    /**
     * List the creation fields for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke(ResourceCreateOrAttachRequest $request)
    {
        if ($request->has('fromResourceId')) {
            return ReplicateViewResource::make($request->fromResourceId)->toResponse($request);
        }

        return CreateViewResource::make()->toResponse($request);
    }

    /**
     * Synchronize the field for creation view.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(ResourceCreateOrAttachRequest $request)
    {
        $resource = $request->has('fromResourceId')
                        ? ReplicateViewResource::make($request->fromResourceId)->newResourceWith($request)
                        : CreateViewResource::make()->newResourceWith($request);

        return response()->json(
            $resource->creationFields($request)
                ->filter(function ($field) use ($request) {
                    return $request->query('field') === $field->attribute &&
                            $request->query('component') === $field->dependentComponentKey();
                })->each->syncDependsOn($request)
                ->first()
        );
    }
}
