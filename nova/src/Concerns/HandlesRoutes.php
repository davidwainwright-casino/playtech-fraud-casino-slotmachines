<?php

namespace Laravel\Nova\Concerns;

use Illuminate\Support\Facades\Route;

trait HandlesRoutes
{
    /**
     * Get url for Laravel Nova.
     *
     * @param  string  $url
     * @return string
     */
    public static function url($url)
    {
        return rtrim(static::path(), '/').'/'.ltrim((string) $url, '/');
    }

    /**
     * Get Route Registrar for Nova.
     *
     * @param  array<int, class-string|string>|null  $middleware
     * @param  string|null  $prefix
     * @return \Illuminate\Routing\RouteRegistrar
     */
    public static function router($middleware = null, $prefix = null)
    {
        return Route::domain(config('nova.domain', null))
                    ->prefix(static::url($prefix))
                    ->middleware($middleware ?? config('nova.middleware', []));
    }
}
