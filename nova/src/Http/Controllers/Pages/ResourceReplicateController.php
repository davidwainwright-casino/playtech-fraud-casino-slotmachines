<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest;

class ResourceReplicateController extends Controller
{
    /**
     * Show Resource Replicate page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceCreateOrAttachRequest  $request
     * @return \Inertia\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(ResourceCreateOrAttachRequest $request)
    {
        $resourceClass = $request->resource();

        return Inertia::render('Nova.Replicate', [
            'resourceName' => $resourceClass::uriKey(),
            'resourceId' => $request->resourceId,
            'viaResource' => $request->query('viaResource') ?? '',
            'viaResourceId' => $request->query('viaResourceId') ?? '',
            'viaRelationship' => $request->query('viaRelationship') ?? '',
        ]);
    }
}
