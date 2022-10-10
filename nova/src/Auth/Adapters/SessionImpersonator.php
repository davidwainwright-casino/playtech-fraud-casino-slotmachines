<?php

namespace Laravel\Nova\Auth\Adapters;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Nova\Contracts\ImpersonatesUsers;
use Laravel\Nova\Nova;

class SessionImpersonator implements ImpersonatesUsers
{
    /**
     * Start impersonating a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return bool
     */
    public function impersonate(Request $request, StatefulGuard $guard, Authenticatable $user)
    {
        return rescue(function () use ($request, $guard, $user) {
            $request->session()->put(
                'nova_impersonated_by', Nova::user($request)->getAuthIdentifier()
            );
            $request->session()->put(
                'nova_impersonated_remember', $guard->viaRemember()
            );

            $novaGuard = config('nova.guard') ?? config('auth.defaults.guard');

            $authGuard = method_exists($guard, 'getName')
                            ? Str::between($guard->getName(), 'login_', '_'.sha1(get_class($guard)))
                            : null;

            if (is_null($authGuard)) {
                return false;
            }

            if ($novaGuard !== $authGuard) {
                $request->session()->put(
                    'nova_impersonated_guard', $authGuard
                );
            }

            $guard->login($user);

            return true;
        }, false);
    }

    /**
     * Stop impersonating the currently impersonated user and revert to the original session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @param  string  $userModel
     * @return bool
     */
    public function stopImpersonating(Request $request, StatefulGuard $guard, string $userModel)
    {
        return rescue(function () use ($request, $guard, $userModel) {
            $impersonator = $userModel::findOrFail($request->session()->get('nova_impersonated_by', null));

            if ($request->session()->has('nova_impersonated_guard')) {
                Auth::guard($request->session()->get('nova_impersonated_guard'))->logout();
            }

            $guard->login($impersonator, $request->session()->get('nova_impersonated_remember') ?? false);

            $this->flushImpersonationData($request);

            return true;
        }, false);
    }

    /**
     * Determine if a user is currently being impersonated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function impersonating(Request $request)
    {
        return $request->session()->has('nova_impersonated_by');
    }

    /**
     * Remove any impersonation data from the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function flushImpersonationData(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->forget('nova_impersonated_by');
            $request->session()->forget('nova_impersonated_guard');
            $request->session()->forget('nova_impersonated_remember');
        }
    }
}
