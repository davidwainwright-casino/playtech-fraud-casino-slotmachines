<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Routing\Controller;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Http\Requests\DeleteResourceRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\URL;

class ResourceDestroyController extends Controller
{
    use DeletesFields;

    /**
     * Destroy the given resource(s).
     *
     * @param  \Laravel\Nova\Http\Requests\DeleteResourceRequest  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function __invoke(DeleteResourceRequest $request)
    {
        $request->chunks(150, function ($models) use ($request) {
            $models->each(function ($model) use ($request) {
                $this->deleteFields($request, $model);

                $uses = class_uses_recursive($model);

                if (in_array(Actionable::class, $uses) && ! in_array(SoftDeletes::class, $uses)) {
                    $model->actions()->delete();
                }

                $model->delete();

                $request->resource()::afterDelete($request, $model);

                Nova::usingActionEvent(function ($actionEvent) use ($model, $request) {
                    $actionEvent->insert(
                        $actionEvent->forResourceDelete(Nova::user($request), collect([$model]))
                            ->map->getAttributes()->all()
                    );
                });
            });
        });

        if ($request->isForSingleResource() && ! is_null($redirect = $request->resource()::redirectAfterDelete($request))) {
            return response()->json([
                'redirect' => URL::make($redirect),
            ]);
        }

        return response()->noContent(200);
    }
}
