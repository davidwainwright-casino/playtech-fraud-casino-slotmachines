<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\RestoreLensResourceRequest;
use Laravel\Nova\Nova;

class LensResourceRestoreController extends Controller
{
    /**
     * Force delete the given resource(s).
     *
     * @param  \Laravel\Nova\Http\Requests\RestoreLensResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RestoreLensResourceRequest $request)
    {
        $request->chunks(150, function ($models) use ($request) {
            $models->each(function ($model) use ($request) {
                $model->restore();

                Nova::usingActionEvent(function ($actionEvent) use ($model, $request) {
                    $actionEvent->insert(
                        $actionEvent->forResourceRestore(Nova::user($request), collect([$model]))
                            ->map->getAttributes()->all()
                    );
                });
            });
        });

        return response()->noContent(200);
    }
}
