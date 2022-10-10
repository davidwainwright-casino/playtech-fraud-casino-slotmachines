<?php

namespace Laravel\Nova\Auth;

use Illuminate\Support\Facades\Gate;

trait Impersonatable
{
    /**
     * Determine if the user can impersonate another user.
     *
     * @return bool
     */
    public function canImpersonate()
    {
        return Gate::forUser($this)->check('viewNova');
    }

    /**
     * Determine if the user can be impersonated.
     *
     * @return bool
     */
    public function canBeImpersonated()
    {
        return true;
    }
}
