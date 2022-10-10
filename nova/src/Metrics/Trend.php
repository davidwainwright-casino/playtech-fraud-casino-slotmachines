<?php

namespace Laravel\Nova\Metrics;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Laravel\Nova\Nova;

abstract class Trend extends RangedMetric
{
    use RoundingPrecision;

    /**
     * Trend metric unit constants.
     */
    const BY_MONTHS = 'month';

    const BY_WEEKS = 'week';

    const BY_DAYS = 'day';

    const BY_HOURS = 'hour';

    const BY_MINUTES = 'minute';

    /**
     * The element's component.
     *
     * @var string
     */
    public $component = 'trend-metric';

    /**
     * Create a new trend metric result.
     *
     * @param  int|float|numeric-string|null  $value
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function result($value = null)
    {
        return new TrendResult($value);
    }

    /**
     * Return a value result showing a count aggregate over months.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function countByMonths($request, $model, $column = null)
    {
        return $this->count($request, $model, self::BY_MONTHS, $column);
    }

    /**
     * Return a value result showing a count aggregate over weeks.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function countByWeeks($request, $model, $column = null)
    {
        return $this->count($request, $model, self::BY_WEEKS, $column);
    }

    /**
     * Return a value result showing a count aggregate over days.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function countByDays($request, $model, $column = null)
    {
        return $this->count($request, $model, self::BY_DAYS, $column);
    }

    /**
     * Return a value result showing a count aggregate over hours.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function countByHours($request, $model, $column = null)
    {
        return $this->count($request, $model, self::BY_HOURS, $column);
    }

    /**
     * Return a value result showing a count aggregate over minutes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function countByMinutes($request, $model, $column = null)
    {
        return $this->count($request, $model, self::BY_MINUTES, $column);
    }

    /**
     * Return a value result showing a count aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  string|null  $column
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function count($request, $model, $unit, $column = null)
    {
        $resource = $model instanceof Builder ? $model->getModel() : new $model;

        $column = $column ?? $resource->getQualifiedCreatedAtColumn();

        return $this->aggregate($request, $model, $unit, 'count', $resource->getQualifiedKeyName(), $column);
    }

    /**
     * Return a value result showing a average aggregate over months.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function averageByMonths($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MONTHS, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a average aggregate over weeks.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function averageByWeeks($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_WEEKS, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a average aggregate over days.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function averageByDays($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_DAYS, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a average aggregate over hours.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function averageByHours($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_HOURS, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a average aggregate over minutes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function averageByMinutes($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MINUTES, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a average aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function average($request, $model, $unit, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, $unit, 'avg', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over months.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sumByMonths($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MONTHS, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over weeks.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sumByWeeks($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_WEEKS, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over days.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sumByDays($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_DAYS, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over hours.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sumByHours($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_HOURS, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over minutes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sumByMinutes($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MINUTES, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a sum aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function sum($request, $model, $unit, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, $unit, 'sum', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over months.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return TrendResult
     */
    public function maxByMonths($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MONTHS, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over weeks.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function maxByWeeks($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_WEEKS, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over days.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function maxByDays($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_DAYS, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over hours.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function maxByHours($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_HOURS, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over minutes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function maxByMinutes($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MINUTES, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a max aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function max($request, $model, $unit, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, $unit, 'max', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over months.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function minByMonths($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MONTHS, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over weeks.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function minByWeeks($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_WEEKS, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over days.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function minByDays($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_DAYS, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over hours.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function minByHours($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_HOURS, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over minutes.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function minByMinutes($request, $model, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, self::BY_MINUTES, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a min aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function min($request, $model, $unit, $column, $dateColumn = null)
    {
        return $this->aggregate($request, $model, $unit, 'min', $column, $dateColumn);
    }

    /**
     * Return a value result showing a aggregate over time.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder|class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @param  string  $unit
     * @param  string  $function
     * @param  \Illuminate\Database\Query\Expression|string  $column
     * @param  string|null  $dateColumn
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    protected function aggregate($request, $model, $unit, $function, $column, $dateColumn = null)
    {
        $query = $model instanceof Builder ? $model : (new $model)->newQuery();

        $timezone = Nova::resolveUserTimezone($request) ?? $this->getDefaultTimezone($request);

        $expression = (string) TrendDateExpressionFactory::make(
            $query, $dateColumn = $dateColumn ?? $query->getModel()->getQualifiedCreatedAtColumn(),
            $unit, $timezone
        );

        $possibleDateResults = $this->getAllPossibleDateResults(
            $startingDate = $this->getAggregateStartingDate($request, $unit, $timezone),
            $endingDate = CarbonImmutable::now($timezone),
            $unit,
            $request->twelveHourTime === 'true'
        );

        $wrappedColumn = $column instanceof Expression
                ? (string) $column
                : $query->getQuery()->getGrammar()->wrap($column);

        $results = $query
                ->select(DB::raw("{$expression} as date_result, {$function}({$wrappedColumn}) as aggregate"))
                ->tap(function ($query) use ($request) {
                    return $this->applyFilterQuery($request, $query);
                })
                ->whereBetween(
                    $dateColumn, $this->formatQueryDateBetween([$startingDate, $endingDate])
                )->groupBy(DB::raw($expression))
                ->orderBy('date_result')
                ->get();

        $results = array_merge($possibleDateResults, $results->mapWithKeys(function ($result) use ($request, $unit) {
            return [$this->formatAggregateResultDate(
                $result->date_result, $unit, $request->twelveHourTime === 'true'
            ) => round($result->aggregate, $this->roundingPrecision, $this->roundingMode)];
        })->all());

        if (count($results) > $request->range) {
            array_shift($results);
        }

        return $this->result(Arr::last($results))->trend(
            $results
        );
    }

    /**
     * Determine the proper aggregate starting date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $unit
     * @param  mixed  $timezone
     * @return \Carbon\CarbonInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getAggregateStartingDate($request, $unit, $timezone)
    {
        $now = CarbonImmutable::now($timezone);

        $range = $request->range;
        $ranges = collect($this->ranges())->keys()->values()->all();

        if (count($ranges) > 0 && ! in_array($range, $ranges)) {
            $range = min($range, max($ranges));
        }

        switch ($unit) {
            case 'month':
                return $now->subMonthsWithoutOverflow($range - 1)->firstOfMonth()->setTime(0, 0);

            case 'week':
                return $now->subWeeks($range - 1)->startOfWeek()->setTime(0, 0);

            case 'day':
                return $now->subDays($range - 1)->setTime(0, 0);

            case 'hour':
                return with($now->subHours($range - 1), function ($now) {
                    return $now->setTimeFromTimeString($now->hour.':00');
                });

            case 'minute':
                return with($now->subMinutes($range - 1), function ($now) {
                    return $now->setTimeFromTimeString($now->hour.':'.$now->minute.':00');
                });

            default:
                throw new InvalidArgumentException('Invalid trend unit provided.');
        }
    }

    /**
     * Format the aggregate result date into a proper string.
     *
     * @param  string  $result
     * @param  string  $unit
     * @param  bool  $twelveHourTime
     * @return string
     */
    protected function formatAggregateResultDate($result, $unit, $twelveHourTime)
    {
        switch ($unit) {
            case 'month':
                return $this->formatAggregateMonthDate($result);

            case 'week':
                return $this->formatAggregateWeekDate($result);

            case 'day':
                return with(Carbon::createFromFormat('Y-m-d', $result), function ($date) {
                    return __($date->format('F')).' '.$date->format('j').', '.$date->format('Y');
                });

            case 'hour':
                return with(Carbon::createFromFormat('Y-m-d H:00', $result), function ($date) use ($twelveHourTime) {
                    return $twelveHourTime
                            ? __($date->format('F')).' '.$date->format('j').' - '.$date->format('g:00 A')
                            : __($date->format('F')).' '.$date->format('j').' - '.$date->format('G:00');
                });

            case 'minute':
            default:
                return with(Carbon::createFromFormat('Y-m-d H:i:00', $result), function ($date) use ($twelveHourTime) {
                    return $twelveHourTime
                            ? __($date->format('F')).' '.$date->format('j').' - '.$date->format('g:i A')
                            : __($date->format('F')).' '.$date->format('j').' - '.$date->format('G:i');
                });
        }
    }

    /**
     * Format the aggregate month result date into a proper string.
     *
     * @param  string  $result
     * @return string
     */
    protected function formatAggregateMonthDate($result)
    {
        [$year, $month] = explode('-', $result);

        return with(Carbon::create((int) $year, (int) $month, 1), function ($date) {
            return __($date->format('F')).' '.$date->format('Y');
        });
    }

    /**
     * Format the aggregate week result date into a proper string.
     *
     * @param  string  $result
     * @return string
     */
    protected function formatAggregateWeekDate($result)
    {
        [$year, $week] = explode('-', $result);

        $isoDate = (new DateTime)->setISODate((int) $year, (int) $week)->setTime(0, 0);

        [$startingDate, $endingDate] = [
            Carbon::instance($isoDate),
            Carbon::instance($isoDate)->endOfWeek(),
        ];

        return __($startingDate->format('F')).' '.$startingDate->format('j').' - '.
               __($endingDate->format('F')).' '.$endingDate->format('j');
    }

    /**
     * Get all of the possible date results for the given units.
     *
     * @param  \Carbon\CarbonInterface  $startingDate
     * @param  \Carbon\CarbonInterface  $endingDate
     * @param  string  $unit
     * @param  bool  $twelveHourTime
     * @return array<string, int>
     */
    protected function getAllPossibleDateResults(CarbonInterface $startingDate, CarbonInterface $endingDate,
        $unit, $twelveHourTime)
    {
        $nextDate = Carbon::instance($startingDate);

        $possibleDateResults[$this->formatPossibleAggregateResultDate(
            $nextDate, $unit, $twelveHourTime
        )] = 0;

        while ($nextDate->lt($endingDate)) {
            if ($unit === self::BY_MONTHS) {
                $nextDate->addMonthWithOverflow();
            } elseif ($unit === self::BY_WEEKS) {
                $nextDate->addWeek();
            } elseif ($unit === self::BY_DAYS) {
                $nextDate->addDay();
            } elseif ($unit === self::BY_HOURS) {
                $nextDate->addHour();
            } elseif ($unit === self::BY_MINUTES) {
                $nextDate->addMinute();
            }

            if ($nextDate->lte($endingDate)) {
                $possibleDateResults[
                    $this->formatPossibleAggregateResultDate(
                        $nextDate, $unit, $twelveHourTime
                    )
                ] = 0;
            }
        }

        return $possibleDateResults;
    }

    /**
     * Format the possible aggregate result date into a proper string.
     *
     * @param  \Carbon\CarbonInterface  $date
     * @param  string  $unit
     * @param  bool  $twelveHourTime
     * @return string
     */
    protected function formatPossibleAggregateResultDate(CarbonInterface $date, $unit, $twelveHourTime)
    {
        switch ($unit) {
            case 'month':
                return __($date->format('F')).' '.$date->format('Y');

            case 'week':
                return __($date->startOfWeek()->format('F')).' '.$date->startOfWeek()->format('j').' - '.
                       __($date->endOfWeek()->format('F')).' '.$date->endOfWeek()->format('j');

            case 'day':
                return __($date->format('F')).' '.$date->format('j').', '.$date->format('Y');

            case 'hour':
                return $twelveHourTime
                        ? __($date->format('F')).' '.$date->format('j').' - '.$date->format('g:00 A')
                        : __($date->format('F')).' '.$date->format('j').' - '.$date->format('G:00');

            case 'minute':
            default:
                return $twelveHourTime
                        ? __($date->format('F')).' '.$date->format('j').' - '.$date->format('g:i A')
                        : __($date->format('F')).' '.$date->format('j').' - '.$date->format('G:i');
        }
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
}
