<?php

namespace Laravel\Nova\Metrics;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Util;

abstract class Partition extends Metric
{
    use RoundingPrecision;

    /**
     * The element's component.
     *
     * @var string
     */
    public $component = 'partition-metric';

    /**
     * Return a partition result showing the segments of a count aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $groupBy
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function count($request, $model, $groupBy, $column = null)
    {
        return $this->aggregate($request, $model, 'count', $column, $groupBy);
    }

    /**
     * Return a partition result showing the segments of an average aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function average($request, $model, $column, $groupBy)
    {
        return $this->aggregate($request, $model, 'avg', $column, $groupBy);
    }

    /**
     * Return a partition result showing the segments of a sum aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function sum($request, $model, $column, $groupBy)
    {
        return $this->aggregate($request, $model, 'sum', $column, $groupBy);
    }

    /**
     * Return a partition result showing the segments of a max aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function max($request, $model, $column, $groupBy)
    {
        return $this->aggregate($request, $model, 'max', $column, $groupBy);
    }

    /**
     * Return a partition result showing the segments of a min aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function min($request, $model, $column, $groupBy)
    {
        return $this->aggregate($request, $model, 'min', $column, $groupBy);
    }

    /**
     * Return a partition result showing the segments of a aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $function
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string  $groupBy
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    protected function aggregate($request, $model, $function, $column, $groupBy)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $wrappedColumn = $column instanceof Expression
                ? (string) $column
                : $query->getQuery()->getGrammar()->wrap(
                    $column ?? $query->getModel()->getQualifiedKeyName()
                );

        $results = $query->select(
            $groupBy, DB::raw("{$function}({$wrappedColumn}) as aggregate")
        )->tap(function ($query) use ($request) {
            return $this->applyFilterQuery($request, $query);
        })->groupBy($groupBy)->get();

        return $this->result($results->mapWithKeys(function ($result) use ($groupBy) {
            return $this->formatAggregateResult($result, $groupBy);
        })->all());
    }

    /**
     * Format the aggregate result for the partition.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $result
     * @param  string  $groupBy
     * @return array
     */
    protected function formatAggregateResult($result, $groupBy)
    {
        $key = with($result->{last(explode('.', $groupBy))}, function ($key) {
            return Util::value($key);
        });

        return [$key => $result->aggregate];
    }

    /**
     * Create a new partition metric result.
     *
     * @param  array<string, int|float>  $value
     * @return \Laravel\Nova\Metrics\PartitionResult
     */
    public function result(array $value)
    {
        return new PartitionResult(collect($value)->map(function ($number) {
            return round($number, $this->roundingPrecision, $this->roundingMode);
        })->toArray());
    }
}
