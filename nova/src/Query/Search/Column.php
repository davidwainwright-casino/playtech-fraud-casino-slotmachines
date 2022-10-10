<?php

namespace Laravel\Nova\Query\Search;

use Illuminate\Database\Query\Expression;

class Column
{
    /**
     * The search column.
     *
     * @var \Illuminate\Database\Query\Expression|string
     */
    public $column;

    /**
     * Construct a new search.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @return void
     */
    public function __construct($column)
    {
        $this->column = $column;
    }

    /**
     * Create Column Search.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @return mixed
     */
    public static function from($column)
    {
        if ($column instanceof Expression) {
            return new static($column);
        }

        if (strpos($column, '->') !== false) {
            return new SearchableJson($column);
        } elseif (strpos($column, '.') !== false) {
            [$relation, $columnName] = explode('.', $column, 2);

            return new SearchableRelation($relation, $columnName);
        }

        return new static($column);
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
        return $query->{$whereOperator}(
            $this->columnName($query),
            $connectionType == 'pgsql' ? 'ilike' : 'like',
            "%{$search}%"
        );
    }

    /**
     * Get the column name.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation  $query
     * @return string
     */
    protected function columnName($query)
    {
        return $this->column instanceof Expression ? $this->column : $query->qualifyColumn($this->column);
    }
}
