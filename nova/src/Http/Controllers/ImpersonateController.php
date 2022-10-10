<?php

namespace Laravel\Nova\Http\Controllers;

use Illuminate\Foundation\Auth\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Contracts\ImpersonatesUsers;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Util;

class ImpersonateController extends Controller
{
    /**
     * Impersonate a user.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Contracts\ImpersonatesUsers  $impersonator
     * @return \Illuminate\Http\JsonResponse
     */
    public function impersonate(NovaRequest $request, ImpersonatesUsers $impersonator)
    {
        $userModel = with(Nova::modelInstanceForKey($request->input('resource')), function ($model) {
            return ! is_null($model) ? get_class($model) : Util::userModel();
        });

        $novaGuard = config('nova.guard') ?? config('auth.defaults.guard');

        $authGuard = Util::sessionAuthGuardForModel($userModel);

        $currentUser = Nova::user($request);

        $user = $userModel::findOrFail($request->input('resourceId'));

        // If we're already impersonating someone, and we want to impersonate
        // someone else, then we'll first have top stop impersonating
        // and reload to 'refresh' back to the 'real' session.
        if ($impersonator->impersonating($request)) {
            $impersonator->stopImpersonating(
                $request,
                Auth::guard($novaGuard),
                Util::userModel()
            );

            return response()->json([
                'redirect' => Nova::url('/'),
            ]);
        }

        // Now that we're guaranteed to be a 'real' user, we'll make sure we're
        // actually trying to impersonate someone besides ourselves, as that
        // would be unnecessary.
        if (! $currentUser->is($user)) {
            abort_unless((optional($currentUser)->canImpersonate() ?? false), 403);
            abort_unless((optional($user)->canBeImpersonated() ?? false), 403);

            $impersonator->impersonate(
                $request,
                Auth::guard($authGuard),
                $user
            );
        }

        return response()->json([
            'redirect' => '/',
        ]);
    }

    /**
     * Stop impersonating a user.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Contracts\ImpersonatesUsers  $impersonator
     * @return \Illuminate\Http\JsonResponse
     */
    public function stopImpersonating(NovaRequest $request, ImpersonatesUsers $impersonator)
    {
        if ($impersonator->impersonating($request)) {
            $impersonator->stopImpersonating(
                $request,
                Auth::guard($guard = config('nova.guard') ?? config('auth.defaults.guard')),
                Util::userModel()
            );
        }

        return response()->json([
            'redirect' => Nova::url('/'),
        ]);
    }
}
