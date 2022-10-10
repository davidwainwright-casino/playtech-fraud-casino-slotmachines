<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Exceptions\ResourceSaveCancelledException;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\URL;
use Laravel\Nova\Util;
use Throwable;

class ResourceUpdateController extends Controller
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
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function __invoke(UpdateResourceRequest $request)
    {
        $model = $request->findModelQuery()->lockForUpdate()->firstOrFail();

        try {
            [$model, $resource] = DB::connection($model->getConnectionName())->transaction(function () use ($request, $model) {
                $resource = $request->newResourceWith($model);
                $resource->authorizeToUpdate($request);
                $resource::validateForUpdate($request, $resource);

                if ($this->modelHasBeenUpdatedSinceRetrieval($request, $model)) {
                    response('', 409)->throwResponse();
                }

                [$model, $callbacks] = $resource::fillForUpdate($request, $model);

                DB::transaction(function () use ($request, $model) {
                    Nova::usingActionEvent(function ($actionEvent) use ($request, $model) {
                        $this->actionEvent = $actionEvent->forResourceUpdate(Nova::user($request), $model);
                        $this->actionEvent->save();
                    });
                });

                if ($model->save() === false) {
                    throw new ResourceSaveCancelledException;
                }

                collect($callbacks)->each->__invoke();

                $resource::afterUpdate($request, $model);

                return [$model, $resource];
            });

            tap(Nova::user($request), function ($user) use ($model) {
                if (get_class($model) === Util::userModel() && $model->is($user)) {
                    $user->refresh();
                }
            });

            return response()->json([
                'id' => $model->getKey(),
                'resource' => $model->attributesToArray(),
                'redirect' => URL::make($resource::redirectAfterUpdate($request, $resource)),
            ]);
        } catch (Throwable $e) {
            optional($this->actionEvent)->delete();
            throw $e;
        }
    }

    /**
     * Determine if the model has been updated since it was retrieved.
     *
     * @param  \Laravel\Nova\Http\Requests\UpdateResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    protected function modelHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $model)
    {
        $resource = $request->newResource();

        // Check to see whether Traffic Cop is enabled for this resource...
        if ($resource::trafficCop($request) === false) {
            return false;
        }

        $column = $model->getUpdatedAtColumn();

        if (! $model->{$column}) {
            return false;
        }

        return $request->input('_retrieved_at') && $model->usesTimestamps() && $model->{$column}->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}
