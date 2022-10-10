<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Resources\LensViewResource;

class LensController extends Controller
{
    /**
     * List the lenses for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(LensRequest $request)
    {
        return response()->json(
            $request->availableLenses()
        );
    }

    /**
     * Get the specified lens and its resources.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(LensRequest $request)
    {
        return LensViewResource::make()->toResponse($request);
    }
}
