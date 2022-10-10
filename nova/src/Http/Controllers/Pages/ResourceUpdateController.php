<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest;

class ResourceUpdateController extends Controller
{
    /**
     * Show Resource Update page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceUpdateOrUpdateAttachedRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(ResourceUpdateOrUpdateAttachedRequest $request)
    {
        $resourceClass = $request->resource();

        return Inertia::render('Nova.Update', [
            'resourceName' => $resourceClass::uriKey(),
            'resourceId' => $request->resourceId,
            'viaResource' => $request->query('viaResource') ?? '',
            'viaResourceId' => $request->query('viaResourceId') ?? '',
            'viaRelationship' => $request->query('viaRelationship') ?? '',
        ]);
    }
}
