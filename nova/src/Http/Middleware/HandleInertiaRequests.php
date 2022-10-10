<?php

namespace Laravel\Nova\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Laravel\Nova\Http\Resources\UserResource;
use Laravel\Nova\Nova;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'nova::layout';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return sprintf('%s:%s', $this->rootView, parent::version($request));
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'novaConfig' => function () use ($request) {
                return Nova::jsonVariables($request);
            },
            'currentUser' => function () use ($request) {
                return with(Nova::user($request), function ($user) use ($request) {
                    return ! is_null($user) ? UserResource::make($user)->toArray($request) : null;
                });
            },
            'validLicense' => function () {
                return Nova::checkLicenseValidity();
            },
        ]);
    }
}
