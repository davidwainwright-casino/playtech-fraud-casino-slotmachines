<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;

class ResourceCreateController extends Controller
{
    /**
     * Show Resource Create page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(ResourceCreateOrAttachRequest $request)
    {
        $resourceClass = $request->resource();

        $resourceClass::authorizeToCreate($request);

        return Inertia::render('Nova.Create', [
            'resourceName' => $resourceClass::uriKey(),
            'viaResource' => $request->query('viaResource') ?? '',
            'viaResourceId' => $request->query('viaResourceId') ?? '',
            'viaRelationship' => $request->query('viaRelationship') ?? '',
        ]);
    }
}
