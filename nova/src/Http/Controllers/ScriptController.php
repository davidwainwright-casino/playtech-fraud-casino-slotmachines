<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class ScriptController extends Controller
{
    /**
     * Serve the requested script.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Script
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(NovaRequest $request)
    {
        $asset = collect(Nova::allScripts())
                    ->filter(function ($asset) use ($request) {
                        return $asset->name() === $request->script;
                    })->first();

        abort_if(is_null($asset), 404);

        return $asset;
    }
}
