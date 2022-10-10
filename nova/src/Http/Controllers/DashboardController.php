<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\DashboardRequest;
use Laravel\Nova\Http\Resources\DashboardViewResource;

class DashboardController extends Controller
{
    /**
     * Return the details for the Dashboard.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardRequest  $request
     * @param  string  $dashboard
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(DashboardRequest $request, $dashboard = 'main')
    {
        return DashboardViewResource::make($dashboard)->toResponse($request);
    }
}
