<?php

namespace Laravel\Nova\Http\Requests;

use Closure;
use Illuminate\Support\Collection;

class DetachResourceRequest extends DeletionRequest
{
    /**
     * Get the selected models for the action in chunks.
     *
     * @param  int  $count
     * @param  \Closure(\Illuminate\Support\Collection):void  $callback
     * @return mixed
     */
    public function chunks($count, Closure $callback)
    {
        $parentResource = $this->findParentResourceOrFail();
        $model = $this->model();

        $this->toSelectedResourceQuery()->when(! $this->allResourcesSelected(), function ($query) {
            $query->whereKey($this->resources);
        })->chunkById($count, function ($models) use ($callback, $parentResource) {
            $models = $this->detachableModels($models, $parentResource);

            if ($models->isNotEmpty()) {
                $callback($models);
            }
        }, $model->getQualifiedKeyName(), $model->getKeyName());
    }

    /**
     * Get the models that may be detached.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @param  \Laravel\Nova\Resource  $parentResource
     * @return \Illuminate\Support\Collection
     */
    protected function detachableModels(Collection $models, $parentResource)
    {
        return $models->filter(function ($model) use ($parentResource) {
            return $parentResource->authorizedToDetach($this, $model, $this->viaRelationship);
        });
    }
}
