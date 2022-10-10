<?php

namespace Laravel\Nova\Query\Search;

use Illuminate\Support\Str;

class SearchableJson extends Column
{
    /**
     * The search JSON seletor path.
     *
     * @var \Illuminate\Database\Query\Expression|string
     */
    public $jsonSelectorPath;

    /**
     * Construct a new search.
     *
     * @param  \Illuminate\Database\Query\Expression|string  $jsonSelectorPath
     * @return void
     */
    public function __construct($jsonSelectorPath)
    {
        $this->jsonSelectorPath = $jsonSelectorPath;
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
        $path = $query->getGrammar()->wrap($this->jsonSelectorPath);
        $likeOperator = $connectionType == 'pgsql' ? 'ilike' : 'like';

        if (in_array($connectionType, ['pgsql', 'sqlite'])) {
            return $query->{$whereOperator}($this->jsonSelectorPath, $likeOperator, "%{$search}%");
        }

        return $query->{$whereOperator.'Raw'}("lower({$path}) {$likeOperator} ?", ['%'.Str::lower($search).'%']);
    }
}
