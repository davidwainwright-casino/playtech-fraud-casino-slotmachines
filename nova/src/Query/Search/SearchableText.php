<?php

namespace Laravel\Nova\Query\Search;

class SearchableText extends Column
{
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
        if (in_array($connectionType, ['mysql', 'pgsql'])) {
            $query->{$whereOperator.'FullText'}(
                $this->columnName($query), $search
            );
        }

        return $query;
    }
}
