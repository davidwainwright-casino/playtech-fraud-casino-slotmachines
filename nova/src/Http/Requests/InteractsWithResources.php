<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Contracts\QueryBuilder;
use Laravel\Nova\Nova;

trait InteractsWithResources
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    /**
     * Determine if the requested resource is soft deleting.
     *
     * @return bool
     */
    public function resourceSoftDeletes()
    {
        $resource = $this->resource();

        return $resource::softDeletes();
    }

    /**
     * Get the class name of the resource being requested.
     *
     * @return class-string<\Laravel\Nova\Resource>
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function resource()
    {
        return tap(once(function () {
            return Nova::resourceForKey($this->route('resource'));
        }), function ($resource) {
            abort_if(is_null($resource), 404);
        });
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @return \Laravel\Nova\Resource
     */
    public function newResource()
    {
        $resource = $this->resource();

        return new $resource($this->model());
    }

    /**
     * Find the resource model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Laravel\Nova\Resource
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findResourceOrFail($resourceId = null)
    {
        return $this->newResourceWith($this->findModelOrFail($resourceId));
    }

    /**
     * Find the model instance for the request or throw an exception.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Model
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findModelOrFail($resourceId = null)
    {
        if ($resourceId) {
            return $this->findModelQuery($resourceId)->firstOrFail();
        }

        return once(function () {
            return $this->findModelQuery()->firstOrFail();
        });
    }

    /**
     * Find the model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function findModel($resourceId = null)
    {
        return rescue(function () use ($resourceId) {
            return $this->findModelOrFail($resourceId);
        }, $this->model(), false);
    }

    /**
     * Get the query to find the model instance for the request.
     *
     * @param  mixed|null  $resourceId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findModelQuery($resourceId = null)
    {
        return app()->make(QueryBuilder::class, [$this->resource()])
                    ->whereKey(
                        $this->newQueryWithoutScopes(),
                        $resourceId ?? $this->resourceId
                    )->toBase();
    }

    /**
     * Get a new instance of the resource being requested.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Laravel\Nova\Resource
     */
    public function newResourceWith($model)
    {
        $resource = $this->resource();

        return new $resource($model);
    }

    /**
     * Get a new query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->model()->newQuery();
    }

    /**
     * Get a new, scopeless query builder for the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newQueryWithoutScopes()
    {
        return $this->model()->newQueryWithoutScopes();
    }

    /**
     * Get a new instance of the underlying model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function model()
    {
        $resource = $this->resource();

        return $resource::newModel();
    }
}
