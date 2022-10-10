<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Laravel\Nova\Http\Resources\IndexViewResource;

class ResourceIndexController extends Controller
{
    /**
     * Show Resource Index page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Inertia\Response
     */
    public function __invoke(ResourceIndexRequest $request)
    {
        $resourceClass = IndexViewResource::make()->authorizedResourceForRequest($request);

        return Inertia::render('Nova.Index', [
            'resourceName' => $resourceClass::uriKey(),
        ]);
    }
}
