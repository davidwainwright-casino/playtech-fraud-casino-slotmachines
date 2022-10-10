<?php

namespace Laravel\Nova\Query\Search;

class SearchableRelation extends Column
{
    /**
     * The relationship name.
     *
     * @var string
     */
    public $relation;

    /**
     * Construct a new search.
     *
     * @param  string  $relation
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @return void
     */
    public function __construct(string $relation, $column)
    {
        $this->relation = $relation;

        parent::__construct($column);
    }

    /**
     * Apply the search.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  string  $search
     * @param  string  $connectionType
     * @param  string  $whereOperator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke($query, $search, string $connectionType, string $whereOperator = 'orWhere')
    {
        return $query->{$whereOperator.'Has'}($this->relation, function ($query) use ($search, $connectionType) {
            return Column::from($this->column)->__invoke(
                $query, $search, $connectionType, 'where'
            );
        });
    }
}
