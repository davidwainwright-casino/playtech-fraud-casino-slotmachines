<?php

namespace Laravel\Nova;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\DestructiveAction;
use Laravel\Nova\Contracts\ImpersonatesUsers;
use Laravel\Nova\Http\Requests\NovaRequest;

trait Authorizable
{
    /**
     * Determine if the given resource is authorizable.
     *
     * @return bool
     */
    public static function authorizable()
    {
        return ! is_null(Gate::getPolicyFor(static::newModel()));
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return;
        }

        $gate = Gate::getPolicyFor(static::newModel());

        if (! is_null($gate) && method_exists($gate, 'viewAny')) {
            $this->authorizeTo($request, 'viewAny');
        }
    }

    /**
     * Determine if the resource should be available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToViewAny(Request $request)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor(static::newModel());

        return ! is_null($gate) && method_exists($gate, 'viewAny')
                        ? Gate::forUser(Nova::user($request))->check('viewAny', get_class(static::newModel()))
                        : true;
    }

    /**
     * Determine if the current user can view the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToView(Request $request)
    {
        $this->authorizeTo($request, 'view');
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToView(Request $request)
    {
        return $this->authorizedTo($request, 'view');
    }

    /**
     * Determine if the current user can create new resources or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public static function authorizeToCreate(Request $request)
    {
        throw_unless(static::authorizedToCreate($request), AuthorizationException::class);
    }

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        if (static::authorizable()) {
            return Gate::forUser(Nova::user($request))->check('create', get_class(static::newModel()));
        }

        return true;
    }

    /**
     * Determine if the current user can update the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToUpdate(Request $request)
    {
        $this->authorizeTo($request, 'update');
    }

    /**
     * Determine if the current user can update the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToUpdate(Request $request)
    {
        return $this->authorizedTo($request, 'update');
    }

    /**
     * Determine if the current user can replicate the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToReplicate(Request $request)
    {
        if (! static::authorizable()) {
            return;
        }

        $gate = Gate::getPolicyFor(static::newModel());

        if (! is_null($gate) && method_exists($gate, 'replicate')) {
            $this->authorizeTo($request, 'replicate');

            return;
        }

        $this->authorizeToCreate($request);
        $this->authorizeToUpdate($request);
    }

    /**
     * Determine if the current user can replicate the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToReplicate(Request $request)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor(static::newModel());

        return ! is_null($gate) && method_exists($gate, 'replicate')
                        ? Gate::forUser(Nova::user($request))->check('replicate', $this->model())
                        : $this->authorizedToCreate($request) && $this->authorizedToUpdate($request);
    }

    /**
     * Determine if the current user can delete the given resource or throw an exception.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeToDelete(Request $request)
    {
        $this->authorizeTo($request, 'delete');
    }

    /**
     * Determine if the current user can delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToDelete(Request $request)
    {
        return $this->authorizedTo($request, 'delete');
    }

    /**
     * Determine if the current user can restore the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToRestore(Request $request)
    {
        return $this->authorizedTo($request, 'restore');
    }

    /**
     * Determine if the current user can force delete the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorizedToForceDelete(Request $request)
    {
        return $this->authorizedTo($request, 'forceDelete');
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAdd(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'add'.class_basename($model);

        return ! is_null($gate) && method_exists($gate, $method)
                        ? Gate::forUser(Nova::user($request))->check($method, $this->model())
                        : true;
    }

    /**
     * Determine if the user can attach any models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttachAny(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'attachAny'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
                    ? Gate::forUser(Nova::user($request))->check($method, [$this->model()])
                    : true;
    }

    /**
     * Determine if the user can attach models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAttach(NovaRequest $request, $model)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'attach'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
                    ? Gate::forUser(Nova::user($request))->check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the user can detach models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @param  string  $relationship
     * @return bool
     */
    public function authorizedToDetach(NovaRequest $request, $model, $relationship)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());
        $method = 'detach'.Str::singular(class_basename($model));

        return ! is_null($gate) && method_exists($gate, $method)
                    ? Gate::forUser(Nova::user($request))->check($method, [$this->model(), $model])
                    : true;
    }

    /**
     * Determine if the user can run the given action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Actions\Action  $action
     * @return bool
     */
    public function authorizedToRunAction(NovaRequest $request, Action $action)
    {
        if ($action instanceof DestructiveAction) {
            return $this->authorizedToRunDestructiveAction($request, $action);
        }

        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());

        $method = 'runAction';

        return ! is_null($gate) && method_exists($gate, $method)
                        ? Gate::forUser(Nova::user($request))->check($method, [$this->model(), $action])
                        : $this->authorizedToUpdate($request);
    }

    /**
     * Determine if the user can run the given action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Actions\DestructiveAction  $action
     * @return bool
     */
    public function authorizedToRunDestructiveAction(NovaRequest $request, DestructiveAction $action)
    {
        if (! static::authorizable()) {
            return true;
        }

        $gate = Gate::getPolicyFor($this->model());

        $method = 'runDestructiveAction';

        return ! is_null($gate) && method_exists($gate, $method)
                        ? Gate::forUser(Nova::user($request))->check($method, [$this->model(), $action])
                        : $this->authorizedToDelete($request);
    }

    /**
     * Determine if the current user can impersonate the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function authorizedToImpersonate(NovaRequest $request)
    {
        $user = Nova::user($request);

        return app(ImpersonatesUsers::class)->impersonating($request) === false
                    && ! $this->resource->is($user)
                    && $this->resource instanceof Authenticatable
                    && (method_exists($this->resource, 'canBeImpersonated') && $this->resource->canBeImpersonated() === true)
                    && (method_exists($user, 'canImpersonate') && $user->canImpersonate() === true);
    }

    /**
     * Determine if the current user has a given ability.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizeTo(Request $request, $ability)
    {
        if (static::authorizable()) {
            Gate::forUser(Nova::user($request))->authorize($ability, $this->resource);
        }
    }

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        return static::authorizable() ? Gate::forUser(Nova::user($request))->check($ability, $this->resource) : true;
    }
}
