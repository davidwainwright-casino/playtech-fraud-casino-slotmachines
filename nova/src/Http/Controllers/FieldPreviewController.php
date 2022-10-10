<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Contracts\Previewable;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldPreviewController extends Controller
{
    /**
     * Delete the file at the given field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(NovaRequest $request)
    {
        $request->validate(['value' => ['required', 'string']]);

        $resource = $request->newResource();

        $resource->authorizeToView($request);

        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Contracts\Previewable $field */
        $field = $resource->updateFields($request)
                    ->whereInstanceOf(Previewable::class)
                    ->findFieldByAttribute($request->field, function () {
                        abort(404);
                    });

        return response()->json([
            'preview' => $field->previewFor($request->value),
        ]);
    }
}
