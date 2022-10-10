<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class TrixAttachmentController extends Controller
{
    /**
     * Store an attachment for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NovaRequest $request)
    {
        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Fields\Trix $field */
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        return response()->json(['url' => call_user_func(
            $field->attachCallback, $request
        )]);
    }

    /**
     * Delete a single, persisted attachment for a Trix field by URL.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyAttachment(NovaRequest $request)
    {
        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Fields\Trix $field */
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        call_user_func(
            $field->detachCallback, $request
        );

        return response()->noContent(200);
    }

    /**
     * Purge all pending attachments for a Trix field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function destroyPending(NovaRequest $request)
    {
        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Fields\Trix $field */
        $field = $request->newResource()
                        ->availableFields($request)
                        ->findFieldByAttribute($request->field, function () {
                            abort(404);
                        });

        call_user_func(
            $field->discardCallback, $request
        );

        return response()->noContent(200);
    }
}
