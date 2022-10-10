<?php

namespace Laravel\Nova\Http\Controllers\Pages;

use Illuminate\Routing\Controller;

class Error403Controller extends Controller
{
    /**
     * Show Nova 403 page using Inertia.
     *
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function __invoke()
    {
        abort(403);
    }
}
