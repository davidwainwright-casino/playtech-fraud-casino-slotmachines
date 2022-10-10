<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\ResourceDetailRequest;

class ResourceDetailController extends Controller
{
    /**
     * Show Resource Detail page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceDetailRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(ResourceDetailRequest $request)
    {
        $resourceClass = $request->resource();

        return Inertia::render('Nova.Detail', [
            'resourceName' => $resourceClass::uriKey(),
            'resourceId' => $request->resourceId,
        ]);
    }
}
