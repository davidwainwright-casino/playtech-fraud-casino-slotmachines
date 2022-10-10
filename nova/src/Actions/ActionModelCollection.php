<?php

namespace Laravel\Nova\Actions;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Laravel\Nova\Http\Requests\ActionRequest;

/**
 * @template TKey of array-key
 * @template TModel of \Illuminate\Database\Eloquent\Model
 *
 * @extends \Illuminate\Database\Eloquent\Collection<TKey, TModel>
 */
class ActionModelCollection extends EloquentCollection
{
    /**
     * Remove models the user does not have permission to execute the action against.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return static
     */
    public function filterForExecution(ActionRequest $request)
    {
        $action = $request->action();
        $isPivotAction = $request->isPivotAction();

        return new static($this->filter(function ($model) use ($request, $action, $isPivotAction) {
            if ($isPivotAction || $action->runCallback) {
                return $action->authorizedToRun($request, $model);
            }

            return $action->authorizedToRun($request, $model) && $this->filterByResourceAuthorization($request, $request->newResourceWith($model), $action);
        }));
    }

    /**
     * Remove models the user does not have permission to execute the action against.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Laravel\Nova\Actions\Action|\Laravel\Nova\Actions\DestructiveAction  $action
     * @return bool
     */
    protected function filterByResourceAuthorization(ActionRequest $request, $resource, $action)
    {
        return $resource->authorizedToRunAction($request, $action);
    }
}
