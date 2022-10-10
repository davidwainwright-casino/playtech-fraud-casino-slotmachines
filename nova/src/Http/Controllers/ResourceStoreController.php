<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Actions\ActionEvent;
use Laravel\Nova\Exceptions\ResourceSaveCancelledException;
use Laravel\Nova\Http\Requests\CreateResourceRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\URL;
use Throwable;

class ResourceStoreController extends Controller
{
    /**
     * The action event for the action.
     *
     * @var \Laravel\Nova\Actions\ActionEvent|null
     */
    protected $actionEvent;

    /**
     * Create a new resource.
     *
     * @param  \Laravel\Nova\Http\Requests\CreateResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(CreateResourceRequest $request)
    {
        $resource = $request->resource();

        $resource::authorizeToCreate($request);

        $resource::validateForCreation($request);

        try {
            $model = DB::connection($resource::newModel()->getConnectionName())->transaction(function () use ($request, $resource) {
                [$model, $callbacks] = $resource::fill(
                    $request, $resource::newModel()
                );

                if ($this->storeResource($request, $model) === false) {
                    throw new ResourceSaveCancelledException();
                }

                DB::transaction(function () use ($request, $model) {
                    Nova::usingActionEvent(function (ActionEvent $actionEvent) use ($request, $model) {
                        $this->actionEvent = $actionEvent->forResourceCreate(Nova::user($request), $model);
                        $this->actionEvent->save();
                    });
                });

                collect($callbacks)->each->__invoke();

                $resource::afterCreate($request, $model);

                return $model;
            });

            return response()->json([
                'id' => $model->getKey(),
                'resource' => $model->attributesToArray(),
                'redirect' => URL::make($resource::redirectAfterCreate($request, $request->newResourceWith($model))),
            ], 201);
        } catch (Throwable $e) {
            optional($this->actionEvent)->delete();
            throw $e;
        }
    }

    /**
     * Save the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\CreateResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function storeResource(CreateResourceRequest $request, Model $model)
    {
        if (! $request->viaRelationship()) {
            return $model->save();
        }

        $relation = tap($request->findParentResourceOrFail(), function ($resource) use ($request) {
            abort_unless($resource->hasRelatableField($request, $request->viaRelationship), 404);
        })->model()->{$request->viaRelationship}();

        if ($relation instanceof HasManyThrough) {
            return $model->save();
        }

        return $relation->save($model);
    }
}
