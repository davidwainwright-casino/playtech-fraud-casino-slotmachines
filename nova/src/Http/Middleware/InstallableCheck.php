<?php

namespace Laravel\Nova\Http\Middleware;

use Laravel\Nova\Events\NovaServiceProviderRegistered;
use Laravel\Nova\Util;

class InstallableCheck
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request):mixed  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return $next($request);
    }
}
