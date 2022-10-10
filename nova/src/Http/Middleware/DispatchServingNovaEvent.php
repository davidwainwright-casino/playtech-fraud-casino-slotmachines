<?php

namespace Laravel\Nova\Http\Middleware;

use Illuminate\Container\Container;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Requests\NovaRequest;

class DispatchServingNovaEvent
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
        ServingNova::dispatch($request);

        Container::getInstance()->forgetInstance(NovaRequest::class);

        return $next($request);
    }
}
