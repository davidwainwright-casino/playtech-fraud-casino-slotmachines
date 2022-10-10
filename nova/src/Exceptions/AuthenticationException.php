<?php

namespace Laravel\Nova\Exceptions;

use Illuminate\Auth\AuthenticationException as BaseAuthenticationException;
use Inertia\Inertia;
use Laravel\Nova\Nova;

class AuthenticationException extends BaseAuthenticationException
{
    /**
     * Render the exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'redirect' => $this->location(),
            ], 401);
        } elseif ($request->is('nova-api/*') || $request->is('nova-vendor/*')) {
            return response(null, 401);
        }

        if ($request->inertia() || config('nova.routes.login', false) !== false) {
            return $this->redirectForInertia($request);
        }

        return redirect()->guest($this->location());
    }

    /**
     * Determine the location the user should be redirected to.
     *
     * @return string
     */
    protected function location()
    {
        return config('nova.routes.login') ?: Nova::url('login');
    }

    /**
     * Redirect request for Inertia.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function redirectForInertia($request)
    {
        tap(redirect(), function ($redirect) use ($request) {
            $url = $redirect->getUrlGenerator();

            $intended = $request->method() === 'GET' && $request->route() && ! $request->expectsJson()
                    ? $url->full()
                    : $url->previous();

            if ($intended) {
                $redirect->setIntendedUrl($intended);
            }
        });

        return Inertia::location($this->location());
    }
}
