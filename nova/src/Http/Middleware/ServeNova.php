<?php

namespace Laravel\Nova\Http\Middleware;

use Laravel\Nova\Events\NovaServiceProviderRegistered;
use Laravel\Nova\Util;

class ServeNova
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
        if (Util::isNovaRequest($request)) {
            NovaServiceProviderRegistered::dispatch();
        }

        return $next($request);
    }
}
