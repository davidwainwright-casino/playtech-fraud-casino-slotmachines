<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Laravel\Nova\Http\Requests\DashboardRequest;
use Laravel\Nova\Http\Resources\DashboardViewResource;

class DashboardController extends Controller
{
    /**
     * Show Resource Create page using Inertia.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardRequest  $request
     * @param  string  $name
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke(DashboardRequest $request, $name = 'main')
    {
        DashboardViewResource::make($name)->authorizedDashboardForRequest($request);

        return Inertia::render('Nova.Dashboard', [
            'name' => $name,
        ]);
    }
}
