<?php

namespace Laravel\Nova\Query;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Query\Search\Column;

class Search
{
    /**
     * The Eloquent Query Builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    public $queryBuilder;

    /**
     * The search keyword.
     *
     * @var string
     */
    public $searchKeyword;

    /**
     * Create a new search builder instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $queryBuilder
     * @param  string  $searchKeyword
     * @return void
     */
    public function __construct($queryBuilder, $searchKeyword)
    {
        $this->queryBuilder = $queryBuilder;
        $this->searchKeyword = $searchKeyword;
    }

    /**
     * Get the raw results of the search.
     *
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @param  array<int, string|\Laravel\Nova\Query\Search\Column>  $searchColumns
     * @return mixed
     */
    public function handle($resourceClass, array $searchColumns)
    {
        return $this->queryBuilder->where(function ($query) use ($searchColumns) {
            $connectionType = $query->getModel()->getConnection()->getDriverName();

            collect($searchColumns)
                ->each(function ($column) use ($query, $connectionType) {
                    if ($column instanceof Column || (! is_string($column) && is_callable($column))) {
                        $column($query, $this->searchKeyword, $connectionType);
                    } else {
                        Column::from($column)->__invoke($query, $this->searchKeyword, $connectionType);
                    }
                });
        });
    }
}
