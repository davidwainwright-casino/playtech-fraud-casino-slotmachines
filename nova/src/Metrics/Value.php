<?php

namespace Laravel\Nova\Metrics;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Nova;

abstract class Value extends RangedMetric
{
    use RoundingPrecision;

    /**
     * The element's component.
     *
     * @var string
     */
    public $component = 'value-metric';

    /**
     * The element's icon.
     *
     * @var string
     */
    public $icon = 'chart-bar';

    /**
     * Set the icon for the metric.
     *
     * @param  string  $icon
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Return a value result showing the growth of an count aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function count($request, $model, $column = null, $dateColumn = null)
    {
        return $this->aggregate($request, $model, 'count', $column, $dateColumn);
    }

    /**
     * Return a value result showing the growth of an average aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function average($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing the growth of a sum aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function sum($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing the growth of a maximum aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function max($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing the growth of a minimum aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function min($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing the growth of a model over a given time frame.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $function
     * @param  \Illuminate\Database\Query\Expression|string|null  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    protected function aggregate($request, $model, $function, $column = null, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $query->tap(function ($query) use ($request) {
            return $this->applyFilterQuery($request, $query);
        });

        $column = $column ?? $query->getModel()->getQualifiedKeyName();

        if ($request->range === 'ALL') {
            return $this->result(
                round(
                    with(clone $query)->{$function}($column),
                    $this->roundingPrecision,
                    $this->roundingMode
                )
            );
        }

        $dateColumn = $dateColumn ?? $query->getModel()->getQualifiedCreatedAtColumn();
        $timezone = Nova::resolveUserTimezone($request) ?? $this->getDefaultTimezone($request);

        $currentRange = $this->currentRange($request->range, $timezone);
        $previousRange = $this->previousRange($request->range, $timezone);

        $previousValue = round(
            with(clone $query)->whereBetween(
                $dateColumn, $this->formatQueryDateBetween($previousRange)
            )->{$function}($column) ?? 0,
            $this->roundingPrecision,
            $this->roundingMode
        );

        return $this->result(
            round(
                with(clone $query)->whereBetween(
                    $dateColumn, $this->formatQueryDateBetween($currentRange)
                )->{$function}($column) ?? 0,
                $this->roundingPrecision,
                $this->roundingMode
            )
        )->previous($previousValue);
    }

    /**
     * Calculate the previous range and calculate any short-cuts.
     *
     * @param  string|int  $range
     * @param  string  $timezone
     * @return array<int, \Carbon\CarbonImmutable>
     */
    protected function previousRange($range, $timezone)
    {
        if ($range == 'TODAY') {
            return [
                CarbonImmutable::now($timezone)->subDay()->startOfDay(),
                CarbonImmutable::now($timezone)->subDay()->endOfDay(),
            ];
        }

        if ($range == 'YESTERDAY') {
            return [
                CarbonImmutable::now($timezone)->subDays(2)->startOfDay(),
                CarbonImmutable::now($timezone)->subDays(2)->endOfDay(),
            ];
        }

        if ($range == 'MTD') {
            return [
                CarbonImmutable::now($timezone)->subMonthWithoutOverflow()->startOfMonth(),
                CarbonImmutable::now($timezone)->subMonthWithoutOverflow(),
            ];
        }

        if ($range == 'QTD') {
            return $this->previousQuarterRange($timezone);
        }

        if ($range == 'YTD') {
            return [
                CarbonImmutable::now($timezone)->subYear()->startOfYear(),
                CarbonImmutable::now($timezone)->subYear(),
            ];
        }

        return [
            CarbonImmutable::now($timezone)->subDays($range * 2),
            CarbonImmutable::now($timezone)->subDays($range)->subSecond(),
        ];
    }

    /**
     * Calculate the previous quarter range.
     *
     * @param  string  $timezone
     * @return array<int, \Carbon\CarbonImmutable>
     */
    protected function previousQuarterRange($timezone)
    {
        return [
            CarbonImmutable::now($timezone)->subQuarterWithOverflow()->startOfQuarter(),
            CarbonImmutable::now($timezone)->subQuarterWithOverflow()->subSecond(),
        ];
    }

    /**
     * Calculate the current range and calculate any short-cuts.
     *
     * @param  string|int  $range
     * @param  string  $timezone
     * @return array<int, \Carbon\CarbonImmutable>
     */
    protected function currentRange($range, $timezone)
    {
        if ($range == 'TODAY') {
            return [
                CarbonImmutable::now($timezone)->startOfDay(),
                CarbonImmutable::now($timezone)->endOfDay(),
            ];
        }

        if ($range == 'YESTERDAY') {
            return [
                CarbonImmutable::now($timezone)->subDay()->startOfDay(),
                CarbonImmutable::now($timezone)->subDay()->endOfDay(),
            ];
        }

        if ($range == 'MTD') {
            return [
                CarbonImmutable::now($timezone)->startOfMonth(),
                CarbonImmutable::now($timezone),
            ];
        }

        if ($range == 'QTD') {
            return $this->currentQuarterRange($timezone);
        }

        if ($range == 'YTD') {
            return [
                CarbonImmutable::now($timezone)->startOfYear(),
                CarbonImmutable::now($timezone),
            ];
        }

        return [
            CarbonImmutable::now($timezone)->subDays($range),
            CarbonImmutable::now($timezone),
        ];
    }

    /**
     * Calculate the previous quarter range.
     *
     * @param  string  $timezone
     * @return array<int, \Carbon\CarbonImmutable>
     */
    protected function currentQuarterRange($timezone)
    {
        return [
            CarbonImmutable::now($timezone)->startOfQuarter(),
            CarbonImmutable::now($timezone),
        ];
    }

    /**
     * Create a new value metric result.
     *
     * @param  int|float|numeric-string|null  $value
     * @return \Laravel\Nova\Metrics\ValueResult
     */
    public function result($value)
    {
        return new ValueResult($value);
    }

    /**
     * Get default timezone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    private function getDefaultTimezone($request)
    {
        return $request->timezone ?? config('app.timezone');
    }

    /**
     * Prepare the metric for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'icon' => $this->icon,
        ]);
    }
}
