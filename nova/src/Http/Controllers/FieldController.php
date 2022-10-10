<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldController extends Controller
{
    /**
     * Retrieve the given field for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NovaRequest $request)
    {
        $resource = $request->newResource();

        $fields = $request->relatable
                        ? $resource->availableFieldsOnIndexOrDetail($request)->whereInstanceOf(RelatableField::class)
                        : $resource->availableFields($request);

        return response()->json(
            $fields->findFieldByAttribute($request->field, function () {
                abort(404);
            })
        );
    }
}
