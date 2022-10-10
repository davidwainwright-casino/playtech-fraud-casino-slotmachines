<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\Deletable;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Http\Requests\DetachResourceRequest;
use Laravel\Nova\Nova;

class ResourceDetachController extends Controller
{
    /**
     * Detach the given resource(s).
     *
     * @param  \Laravel\Nova\Http\Requests\DetachResourceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(DetachResourceRequest $request)
    {
        $parent = tap($request->findParentResourceOrFail(), function ($resource) use ($request) {
            abort_unless($resource->hasRelatableField($request, $request->viaRelationship), 409);
        })->model();

        $relation = $parent->{$request->viaRelationship}();

        $accessor = $relation->getPivotAccessor();

        $accessorKeyName = transform($relation->getPivotClass(), function ($pivotClass) {
            return (new $pivotClass())->getKeyName();
        });

        $inPivots = $request->resources !== 'all' ? $request->pivots : null;

        $request->chunks(150, function ($models) use ($accessor, $accessorKeyName, $inPivots, $parent, $request) {
            foreach ($models as $model) {
                $pivot = $model->{$accessor};

                if (empty($inPivots) || in_array($pivot->getAttribute($accessorKeyName), $inPivots)) {
                    $this->deletePivot(
                        $request, $pivot, $model, $parent
                    );
                }
            }
        });

        return response()->noContent(200);
    }

    /**
     * Delete pivot relations from model.
     *
     * @param  \Laravel\Nova\Http\Requests\DetachResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $pivot
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @return void
     */
    protected function deletePivot(DetachResourceRequest $request, $pivot, $model, $parent)
    {
        $this->deletePivotFields(
            $request, $resource = $request->newResourceWith($model), $pivot
        );

        $pivot->delete();

        Nova::usingActionEvent(function ($actionEvent) use ($pivot, $model, $parent, $request) {
            $actionEvent->insert(
                $actionEvent->forResourceDetach(
                    Nova::user($request), $parent, collect([$model]), $pivot->getMorphClass()
                )->map->getAttributes()->all()
            );
        });
    }

    /**
     * Delete the pivot fields on the given pivot model.
     *
     * @param  \Laravel\Nova\Http\Requests\DetachResourceRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Illuminate\Database\Eloquent\Model  $pivot
     * @return void
     */
    protected function deletePivotFields(DetachResourceRequest $request, $resource, $pivot)
    {
        $resource->resolvePivotFields($request, $request->viaResource)
            ->whereInstanceOf(Deletable::class)
            ->filter->isPrunable()
            ->each(function ($field) use ($request, $pivot) {
                DeleteField::forRequest($request, $field, $pivot)->save();
            });
    }
}
