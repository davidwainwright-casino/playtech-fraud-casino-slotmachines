<?php

namespace Laravel\Nova\Http\Requests;

use Laravel\Nova\Contracts\QueryBuilder;

class ResourceIndexRequest extends NovaRequest
{
    use CountsResources, QueriesResources;

    /**
     * Get the paginator instance for the index request.
     *
     * @return array
     */
    public function searchIndex()
    {
        return app()->make(QueryBuilder::class, [$this->resource()])->search(
            $this, $this->newQuery(), $this->search,
            $this->filters()->all(), $this->orderings(), $this->trashed()
        )->paginate((int) $this->perPage());
    }

    /**
     * Get the count of the resources.
     *
     * @return int
     */
    public function toCount()
    {
        return app()->make(QueryBuilder::class, [$this->resource()])->search(
            $this, $this->newQuery(), $this->search,
            $this->filters()->all(), $this->orderings(), $this->trashed()
        )->toBaseQueryBuilder()->getCountForPagination();
    }

    /**
     * Get per page.
     *
     * @return int
     */
    public function perPage()
    {
        $resource = $this->resource();

        if ($this->viaRelationship()) {
            return (int) $resource::$perPageViaRelationship;
        }

        $perPageOptions = $resource::perPageOptions();

        if (empty($perPageOptions)) {
            $perPageOptions = [$resource::newModel()->getPerPage()];
        }

        return (int) in_array($this->perPage, $perPageOptions) ? $this->perPage : $perPageOptions[0];
    }
}
