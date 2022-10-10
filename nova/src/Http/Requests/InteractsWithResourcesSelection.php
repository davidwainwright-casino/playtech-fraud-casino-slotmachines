<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property-read int|null $resourceId
 * @property-read array|string|null $resources
 */
trait InteractsWithResourcesSelection
{
    /**
     * Determine if currently all resources is selected.
     *
     * @return bool
     */
    public function allResourcesSelected()
    {
        return $this->resources === 'all';
    }

    /**
     * Get selected resources.
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|null
     */
    public function selectedResources()
    {
        if ($this->allResourcesSelected()) {
            return null;
        }

        $resourceIds = array_filter(! empty($this->resources) ? Arr::wrap($this->resources) : [$this->resourceId]);

        if (count($resourceIds) < 1) {
            return $this->resource instanceof Model ? $this->resource->newCollection() : collect();
        }

        return $this->newQueryWithoutScopes()
                    ->whereKey($resourceIds)
                    ->get();
    }
}
