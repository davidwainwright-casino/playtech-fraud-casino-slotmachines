<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Resources\LensViewResource;

class LensController extends Controller
{
    /**
     * Show Resource Lens page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(LensRequest $request)
    {
        $lens = LensViewResource::make()->authorizedLensForRequest($request);

        return Inertia::render('Nova.Lens', [
            'resourceName' => $request->route('resource'),
            'lens' => $lens->uriKey(),
        ]);
    }
}
