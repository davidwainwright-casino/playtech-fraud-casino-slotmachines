<?php

namespace Laravel\Nova\Http\Requests;

use Closure;

/**
 * @property-read string|array<int, mixed> $resources
 */
class DeletionRequest extends NovaRequest
{
    use QueriesResources;

    /**
     * Get the selected models for the action in chunks.
     *
     * @param  int  $count
     * @param  \Closure(\Illuminate\Support\Collection):void  $callback
     * @param  \Closure(\Illuminate\Support\Collection):\Illuminate\Support\Collection  $authCallback
     * @return mixed
     */
    protected function chunkWithAuthorization($count, Closure $callback, Closure $authCallback)
    {
        $model = $this->model();

        $this->toSelectedResourceQuery()->when(! $this->allResourcesSelected(), function ($query) {
            $query->whereKey($this->resources);
        })->tap(function ($query) {
            $query->getQuery()->orders = [];
        })->chunkById($count, function ($models) use ($callback, $authCallback) {
            $models = $authCallback($models);

            if ($models->isNotEmpty()) {
                $callback($models);
            }
        }, $model->getQualifiedKeyName(), $model->getKeyName());
    }

    /**
     * Get the query for the models that were selected by the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function toSelectedResourceQuery()
    {
        if ($this->allResourcesSelected()) {
            return $this->toQuery();
        }

        return $this->newQueryWithoutScopes();
    }
}
