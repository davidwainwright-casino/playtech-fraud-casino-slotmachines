<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\DeleteField;
use Laravel\Nova\Http\Requests\PivotFieldDestroyRequest;
use Laravel\Nova\Nova;

class PivotFieldDestroyController extends Controller
{
    /**
     * Delete the file at the given field.
     *
     * @param  \Laravel\Nova\Http\Requests\PivotFieldDestroyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PivotFieldDestroyRequest $request)
    {
        $request->authorizeForAttachment();

        DeleteField::forRequest(
            $request, $request->findFieldOrFail(),
            $pivot = $request->findPivotModel()
        )->save();

        Nova::usingActionEvent(function ($actionEvent) use ($request, $pivot) {
            $actionEvent->forAttachedResourceUpdate(
                $request, $request->findModelOrFail(), $pivot
            )->save();
        });

        return response()->noContent(200);
    }
}
