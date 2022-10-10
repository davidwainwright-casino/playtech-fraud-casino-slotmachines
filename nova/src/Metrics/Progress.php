<?php

namespace Laravel\Nova\Metrics;

use Illuminate\Database\Eloquent\Builder;

abstract class Progress extends Metric
{
    use RoundingPrecision;

    /**
     * The element's component.
     *
     * @var string
     */
    public $component = 'progress-metric';

    /**
     * Return a progress result showing the growth of an count aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  callable(\Illuminate\Database\Eloquent\Builder):void  $progress
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  int|float|null  $target
     * @return \Laravel\Nova\Metrics\ProgressResult
     */
    public function count($request, $model, callable $progress, $column = null, $target = null)
    {
        return $this->aggregate($request, $model, 'count', $column, $progress, $target);
    }

    /**
     * Return a progress result showing the growth of a sum aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  callable(\Illuminate\Database\Eloquent\Builder):void  $progress
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  int|float|null  $target
     * @return \Laravel\Nova\Metrics\ProgressResult
     */
    public function sum($request, $model, callable $progress, $column, $target = null)
    {
        return $this->aggregate($request, $model, 'sum', $column, $progress, $target);
    }

    /**
     * Return a progress result showing the segments of a aggregate.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $function
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  callable(\Illuminate\Database\Eloquent\Builder):void  $progress
     * @param  int|float|null  $target
     * @return \Laravel\Nova\Metrics\ProgressResult
     */
    protected function aggregate($request, $model, $function, $column, callable $progress, $target = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $column = $column ?? $query->getModel()->getQualifiedKeyName();

        $query->tap(function ($query) use ($request) {
            return $this->applyFilterQuery($request, $query);
        });

        return $this->result(
            round(
                with(clone $query)->tap(function ($query) use ($progress) {
                    call_user_func($progress, $query);
                })->{$function}($column) ?? 0,
                $this->roundingPrecision,
                $this->roundingMode
            ),
            $target ?? round(
                with(clone $query)->{$function}($column) ?? 0,
                $this->roundingPrecision,
                $this->roundingMode
            )
        );
    }

    /**
     * Create a new progress metric result.
     *
     * @param  int|float  $value
     * @param  int|float  $target
     * @return \Laravel\Nova\Metrics\ProgressResult
     */
    public function result($value, $target)
    {
        return new ProgressResult($value, $target);
    }
}
