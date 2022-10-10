<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;

class Error404Controller extends Controller
{
    /**
     * Show Nova 404 page using Inertia.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function __invoke()
    {
        abort(404);
    }
}
