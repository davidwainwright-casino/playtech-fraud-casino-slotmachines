<?php

namespace Laravel\Nova\Query\Search;

class PrimaryKey extends Column
{
    /**
     * Max primary key size.
     *
     * @var int
     */
    protected $maxPrimaryKeySize;

    /**
     * Construct a new search.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  int  $maxPrimaryKeySize
     * @return void
     */
    public function __construct($column, $maxPrimaryKeySize = PHP_INT_MAX)
    {
        $this->column = $column;
        $this->maxPrimaryKeySize = $maxPrimaryKeySize;
    }

    /**
     * Apply the search.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @param  string|int  $search
     * @param  string  $connectionType
     * @param  string  $whereOperator
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke($query, $search, string $connectionType, string $whereOperator = 'orWhere')
    {
        $model = $query->getModel();

        $canSearchPrimaryKey = ctype_digit($search) &&
                               in_array($model->getKeyType(), ['int', 'integer']) &&
                               ($connectionType != 'pgsql' || $search <= $this->maxPrimaryKeySize);

        if (! $canSearchPrimaryKey) {
            return parent::__invoke($query, $search, $connectionType, $whereOperator);
        }

        return $query->{$whereOperator}($model->getQualifiedKeyName(), $search);
    }
}
