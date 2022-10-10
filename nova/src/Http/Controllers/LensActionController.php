<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionCollection;
use Laravel\Nova\Http\Requests\LensActionRequest;
use Laravel\Nova\Http\Requests\LensRequest;

class LensActionController extends Controller
{
    /**
     * List the actions for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(LensRequest $request)
    {
        $lens = $request->lens();

        return response()->json(with([
            'actions' => $lens->availableActionsOnIndex($request),
            'pivotActions' => [
                'name' => $request->pivotName(),
                'actions' => $lens->availablePivotActions($request),
            ],
            'counts' => $lens->resolveActions($request)->countsByTypeOnIndex(),
        ], function ($payload) use ($lens, $request) {
            $actionCounts = $lens->resolveActions($request)->countsByTypeOnIndex();
            $pivotActionCounts = ActionCollection::make($payload['pivotActions']['actions'])->countsByTypeOnIndex();

            $payload['counts'] = [
                'standalone' => $actionCounts['standalone'] + $pivotActionCounts['standalone'],
                'resource' => $actionCounts['resource'] + $pivotActionCounts['resource'],
            ];

            return $payload;
        }));
    }

    /**
     * Perform an action on the specified resources.
     *
     * @param  \Laravel\Nova\Http\Requests\LensActionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LensActionRequest $request)
    {
        $request->validateFields();

        return $request->action()->handleRequest($request);
    }

    /**
     * Sync an action field on the specified resources.
     *
     * @param  \Laravel\Nova\Http\Requests\LensActionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(LensActionRequest $request)
    {
        $action = $request->lens()->availableActions($request)
            ->first(function ($action) use ($request) {
                return $action->uriKey() === $request->query('action');
            });

        abort_unless($action instanceof Action, 404);

        return response()->json(
            collect($action->fields($request))
                ->filter(function ($field) use ($request) {
                    return $request->query('field') === $field->attribute &&
                        $request->query('component') === $field->dependentComponentKey();
                })->each->syncDependsOn($request)
                ->first()
        );
    }
}
