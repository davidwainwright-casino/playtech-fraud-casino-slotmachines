<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Http\Requests\NovaRequest;

class AssociatableController extends Controller
{
    /**
     * List the available related resources for a given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function __invoke(NovaRequest $request)
    {
        $field = $request->newResource()
                    ->availableFieldsOnIndexOrDetail($request)
                    ->whereInstanceOf(RelatableField::class)
                    ->findFieldByAttribute($request->field, function () {
                        abort(404);
                    });

        $withTrashed = $this->shouldIncludeTrashed(
            $request, $associatedResource = $field->resourceClass
        );

        $limit = $associatedResource::usesScout()
                    ? $associatedResource::$scoutSearchResults
                    : $associatedResource::$relatableSearchResults;

        return [
            'resources' => $field->buildAssociatableQuery($request, $withTrashed)
                        ->take($limit)
                        ->get()
                        ->mapInto($field->resourceClass)
                        ->filter->authorizedToAdd($request, $request->model())
                        ->map(function ($resource) use ($request, $field) {
                            return $field->formatAssociatableResource($request, $resource);
                        })->when(optional($field)->shouldReorderAssociatableValues($request) ?? true, function ($collection) {
                            return $collection->sortBy('display');
                        })->values(),
            'softDeletes' => $associatedResource::softDeletes(),
            'withTrashed' => $withTrashed,
        ];
    }

    /**
     * Determine if the query should include trashed models.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $associatedResource
     * @return bool
     */
    protected function shouldIncludeTrashed(NovaRequest $request, $associatedResource)
    {
        if ($request->withTrashed === 'true') {
            return true;
        }

        $associatedModel = $associatedResource::newModel();

        if ($request->current && empty($request->search) && $associatedResource::softDeletes()) {
            $associatedModel = $associatedModel->newQueryWithoutScopes()->find($request->current);

            return $associatedModel ? $associatedModel->trashed() : false;
        }

        return false;
    }
}
