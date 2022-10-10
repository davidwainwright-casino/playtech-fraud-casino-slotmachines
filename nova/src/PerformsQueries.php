<?php

namespace Laravel\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Query\ApplySoftDeleteConstraint;
use Laravel\Nova\Query\Search;
use Laravel\Nova\Query\Search\PrimaryKey;

trait PerformsQueries
{
    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @param  array<int, \Laravel\Nova\Query\ApplyFilter>  $filters
     * @param  array<string, string>  $orderings
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function buildIndexQuery(NovaRequest $request, $query, $search = null,
        array $filters = [], array $orderings = [],
        $withTrashed = TrashedStatus::DEFAULT)
    {
        return static::applyOrderings(static::applyFilters(
            $request, static::initializeQuery($request, $query, (string) $search, $withTrashed), $filters
        ), $orderings)->tap(function ($query) use ($request) {
            static::indexQuery($request, $query->with(static::$with));
        });
    }

    /**
     * Initialize the given index query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQuery(NovaRequest $request, $query, $search, $withTrashed)
    {
        if (empty(trim($search))) {
            return static::applySoftDeleteConstraint($query, $withTrashed);
        }

        return static::usesScout()
                ? static::initializeQueryUsingScout($request, $query, $search, $withTrashed)
                : static::applySearch(static::applySoftDeleteConstraint($query, $withTrashed), $search);
    }

    /**
     * Apply the search query to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applySearch($query, $search)
    {
        $modelKeyName = $query->getModel()->getKeyName();

        $searchColumns = collect(static::searchableColumns() ?? [])
                            ->transform(function ($column) use ($modelKeyName) {
                                if ($column === $modelKeyName) {
                                    return new PrimaryKey($column, static::maxPrimaryKeySize());
                                }

                                return $column;
                            })->all();

        return static::initializeSearch($query, $search, $searchColumns);
    }

    /**
     * Initialize the search configuration.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  array  $searchColumns
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeSearch($query, $search, $searchColumns)
    {
        return app(Search::class, [
            'queryBuilder' => $query,
            'searchKeyword' => $search,
        ])->handle(__CLASS__, $searchColumns);
    }

    /**
     * Initialize the given index query using Laravel Scout.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $search
     * @param  string  $withTrashed
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function initializeQueryUsingScout(NovaRequest $request, $query, $search, $withTrashed)
    {
        $keys = static::buildIndexQueryUsingScout($request, $search, $withTrashed)->get()->map->getKey();

        return static::applySoftDeleteConstraint(
            $query->whereIn(static::newModel()->getQualifiedKeyName(), $keys->all()), $withTrashed
        );
    }

    /**
     * Build an "index" result for the given resource using Scout.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string|null  $search
     * @param  string  $withTrashed
     * @return \Laravel\Scout\Builder
     */
    public static function buildIndexQueryUsingScout(NovaRequest $request, $search = null,
        $withTrashed = TrashedStatus::DEFAULT)
    {
        return tap(static::applySoftDeleteConstraint(
            static::newModel()->search($search), $withTrashed
        ), function ($scoutBuilder) use ($request) {
            static::scoutQuery($request, $scoutBuilder);
        })->take(static::$scoutSearchResults);
    }

    /**
     * Scope the given query for the soft delete state.
     *
     * @param  mixed  $query
     * @param  string  $withTrashed
     * @return mixed
     */
    protected static function applySoftDeleteConstraint($query, $withTrashed)
    {
        return static::softDeletes()
                ? (new ApplySoftDeleteConstraint)->__invoke($query, $withTrashed)
                : $query;
    }

    /**
     * Apply any applicable filters to the query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<int, \Laravel\Nova\Query\ApplyFilter>  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyFilters(NovaRequest $request, $query, array $filters)
    {
        collect($filters)->each->__invoke($request, $query);

        return $query;
    }

    /**
     * Apply any applicable orderings to the query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array<string, string>  $orderings
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected static function applyOrderings($query, array $orderings)
    {
        $orderings = array_filter($orderings);

        if (empty($orderings)) {
            return empty($query->getQuery()->orders) && ! static::usesScout()
                        ? $query->latest($query->getModel()->getQualifiedKeyName())
                        : $query;
        }

        foreach ($orderings as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query;
    }

    /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Scout\Builder  $query
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build an "edit" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function editQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "replicate" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function replicateQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "relatable" query for the given resource.
     *
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return $query;
    }
}
