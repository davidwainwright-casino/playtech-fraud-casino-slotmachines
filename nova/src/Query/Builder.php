<?php

namespace Laravel\Nova\Query;

use Illuminate\Container\Container;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\LazyCollection;
use Laravel\Nova\Contracts\QueryBuilder;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\TrashedStatus;
use Laravel\Scout\Builder as ScoutBuilder;
use RuntimeException;

class Builder implements QueryBuilder
{
    /**
     * The resource class.
     *
     * @var class-string<\Laravel\Nova\Resource>
     */
    protected $resourceClass;

    /**
     * The original query builder instance.
     *
     * @var \Illuminate\Database\Eloquent\Builder|null
     */
    protected $originalQueryBuilder;

    /**
     * The query builder instance.
     *
     * @var \Laravel\Scout\Builder|\Illuminate\Database\Eloquent\Builder|null
     */
    protected $queryBuilder;

    /**
     * Optional callbacks before model query execution.
     *
     * @var array<int, callable(\Illuminate\Database\Eloquent\Builder):void>
     */
    protected $queryCallbacks = [];

    /**
     * Determine query callbacks has been applied.
     *
     * @var bool
     */
    protected $appliedQueryCallbacks = false;

    /**
     * Construct a new query builder for a resource.
     *
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return void
     */
    public function __construct($resourceClass)
    {
        $this->resourceClass = $resourceClass;
    }

    /**
     * Build a "whereKey" query for the given resource.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $key
     * @return $this
     */
    public function whereKey($query, $key)
    {
        $this->setOriginalQueryBuilder($this->queryBuilder = $query);

        $this->tap(function ($query) use ($key) {
            $query->whereKey($key);
        });

        return $this;
    }

    /**
     * Build a "search" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string|null  $search
     * @param  array<int, \Laravel\Nova\Query\ApplyFilter>  $filters
     * @param  array<string, string>  $orderings
     * @param  string  $withTrashed
     * @return $this
     */
    public function search(NovaRequest $request, $query, $search = null,
        array $filters = [], array $orderings = [],
        $withTrashed = TrashedStatus::DEFAULT)
    {
        $this->setOriginalQueryBuilder($query);

        $hasSearchKeyword = ! empty(trim($search ?? ''));
        $hasOrderings = collect($orderings)->filter()->isNotEmpty();

        if ($this->resourceClass::usesScout()) {
            if ($hasSearchKeyword) {
                $this->queryBuilder = $this->resourceClass::buildIndexQueryUsingScout($request, $search, $withTrashed);
                $search = '';
            }

            if (! $hasSearchKeyword && ! $hasOrderings) {
                $this->tap(function ($query) {
                    $query->latest($query->getModel()->getQualifiedKeyName());
                });
            }
        }

        if (! isset($this->queryBuilder)) {
            $this->queryBuilder = $query;
        }

        $this->tap(function ($query) use ($request, $search, $filters, $orderings, $withTrashed) {
            $this->resourceClass::buildIndexQuery(
                $request, $query, $search, $filters, $orderings, $withTrashed
            );
        });

        return $this;
    }

    /**
     * Pass the query to a given callback.
     *
     * @param  callable(\Illuminate\Database\Eloquent\Builder):void  $callback
     * @return $this
     */
    public function tap($callback)
    {
        $this->queryCallbacks[] = $callback;

        return $this;
    }

    /**
     * Set the "take" directly to Scout or Eloquent builder.
     *
     * @param  int  $limit
     * @return $this
     */
    public function take($limit)
    {
        $this->queryBuilder->take($limit);

        return $this;
    }

    /**
     * Defer setting a "limit" using query callback and only executed via Eloquent builder.
     *
     * @param  int  $limit
     * @return $this
     */
    public function limit($limit)
    {
        return $this->tap(function ($query) use ($limit) {
            $query->limit($limit);
        });
    }

    /**
     * Get the results of the search.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function get()
    {
        return $this->applyQueryCallbacks($this->queryBuilder)->get();
    }

    /**
     * Get a lazy collection for the given query by chunks of the given size.
     *
     * @param  int  $chunkSize
     * @return \Illuminate\Support\LazyCollection
     */
    public function lazy($chunkSize = 1000)
    {
        if (! method_exists($this->queryBuilder, 'lazy')) {
            return $this->cursor();
        }

        return $this->applyQueryCallbacks($this->queryBuilder)
                    ->lazy($chunkSize);
    }

    /**
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function cursor()
    {
        $queryBuilder = $this->applyQueryCallbacks($this->queryBuilder);

        if (method_exists($queryBuilder, 'cursor')) {
            return $queryBuilder->cursor();
        }

        return LazyCollection::make(function () use ($queryBuilder) {
            yield from $queryBuilder->get()
                ->each(function ($result) {
                    yield $result;
                });
        });
    }

    /**
     * Get the paginated results of the query.
     *
     * @param  int  $perPage
     * @return array{0: \Illuminate\Contracts\Pagination\Paginator, 1: int|null, 2: bool}
     */
    public function paginate($perPage)
    {
        $queryBuilder = $this->applyQueryCallbacks($this->queryBuilder);

        if (! $queryBuilder instanceof ScoutBuilder) {
            return [
                $queryBuilder->simplePaginate($perPage),
                $this->getCountForPagination(),
                true,
            ];
        }

        $scoutPaginated = $queryBuilder->paginate($perPage);

        $items = $scoutPaginated->items();

        $hasMorePages = ($scoutPaginated->perPage() * $scoutPaginated->currentPage()) < $scoutPaginated->total();

        return [
            Container::getInstance()->makeWith(Paginator::class, [
                'items' => $items,
                'perPage' => $scoutPaginated->perPage(),
                'currentPage' => $scoutPaginated->currentPage(),
                'options' => $scoutPaginated->getOptions(),
            ])->hasMorePagesWhen($hasMorePages),
            $scoutPaginated->total(),
            false,
        ];
    }

    /**
     * Get the count of the total records for the paginator.
     *
     * @return int|null
     */
    public function getCountForPagination()
    {
        return $this->toBaseQueryBuilder()->getCountForPagination();
    }

    /**
     * Convert the query builder to an Eloquent query builder (skip using Scout).
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toBase()
    {
        return $this->applyQueryCallbacks($this->originalQueryBuilder);
    }

    /**
     * Convert the query builder to an fluent query builder (skip using Scout).
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function toBaseQueryBuilder()
    {
        return $this->toBase()->toBase();
    }

    /**
     * Set original query builder instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $queryBuilder
     * @return void
     */
    protected function setOriginalQueryBuilder($queryBuilder)
    {
        if (isset($this->originalQueryBuilder)) {
            throw new RuntimeException('Unable to override $originalQueryBuilder, please create a new '.self::class);
        }

        $this->originalQueryBuilder = $queryBuilder;
    }

    /**
     * Apply any query callbacks to the query builder.
     *
     * @param  \Laravel\Scout\Builder|\Illuminate\Database\Eloquent\Builder  $queryBuilder
     * @return \Laravel\Scout\Builder|\Illuminate\Database\Eloquent\Builder
     */
    protected function applyQueryCallbacks($queryBuilder)
    {
        if ($this->appliedQueryCallbacks === true) {
            return $queryBuilder;
        }

        $callback = function ($queryBuilder) {
            collect($this->queryCallbacks)
                ->filter()
                ->each(function ($callback) use ($queryBuilder) {
                    call_user_func($callback, $queryBuilder);
                });
        };

        if ($queryBuilder instanceof ScoutBuilder) {
            $queryBuilder->query($callback);
        } else {
            $queryBuilder->tap($callback);
        }

        $this->appliedQueryCallbacks = true;

        return $queryBuilder;
    }
}
