<?php

namespace Laravel\Nova\Query\Search;

class SearchableMorphToRelation extends SearchableRelation
{
    /**
     * The available morph types.
     *
     * @var array<int, class-string<\Illuminate\Database\Eloquent\Model|\Laravel\Nova\Resource>|string>
     */
    public $types = [];

    /**
     * Construct a new search.
     *
     * @param  string  $relation
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  array<int, class-string<\Illuminate\Database\Eloquent\Model|\Laravel\Nova\Resource>|string>  $types
     * @return void
     */
    public function __construct(string $relation, $column, array $types = [])
    {
        $this->types = $types;

        parent::__construct($relation, $column);
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
        return $query->{$whereOperator.'HasMorph'}($this->relation, $this->morphTypes(), function ($query) use ($search, $connectionType) {
            return Column::from($this->column)->__invoke(
                $query, $search, $connectionType, 'where'
            );
        });
    }

    /**
     * Get available morph types.
     *
     * @return array<int, class-string<\Illuminate\Database\Eloquent\Model>|string>|string
     */
    protected function morphTypes()
    {
        if (empty($this->types)) {
            return '*';
        }

        return collect($this->types)
            ->map(function ($resource) {
                return $resource::$model ?? $resource;
            })->all();
    }
}
