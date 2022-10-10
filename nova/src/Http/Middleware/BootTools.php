<?php

namespace Laravel\Nova\Http\Middleware;

use Laravel\Nova\Nova;

class BootTools
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
        Nova::bootTools($request);

        return $next($request);
    }
}
