<?php

namespace Laravel\Nova\Metrics;

use DateInterval;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

abstract class Metric extends Card
{
    use HasHelpText,
        ResolvesFilters;

    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    public $name;

    /**
     * Indicates whether the metric should be refreshed when actions run.
     *
     * @var bool
     */
    public $refreshWhenActionRuns = false;

    /**
     * Indicates whether the metric should be refreshed when a filter is changed.
     *
     * @var bool
     */
    public $refreshWhenFiltersChange = false;

    /**
     * Calculate the metric's value.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function resolve(NovaRequest $request)
    {
        $resolver = $this->getResolver($request);

        if ($cacheFor = $this->cacheFor()) {
            $cacheFor = is_numeric($cacheFor) ? new DateInterval(sprintf('PT%dS', $cacheFor * 60)) : $cacheFor;

            return Cache::remember(
                $this->getCacheKey($request),
                $cacheFor,
                $resolver
            );
        }

        return $resolver();
    }

    /**
     * Return a resolver function for the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Closure
     */
    public function getResolver(NovaRequest $request)
    {
        return function () use ($request) {
            return $this->onlyOnDetail
                ? $this->calculate($request, $request->findModelOrFail())
                : $this->calculate($request);
        };
    }

    /**
     * Get the appropriate cache key for the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    public function getCacheKey(NovaRequest $request)
    {
        return sprintf(
            'nova.metric.%s.%s.%s.%s.%s.%s',
            $this->uriKey(),
            $request->input('range', 'no-range'),
            $request->input('timezone', 'no-timezone'),
            $request->input('twelveHourTime', 'no-12-hour-time'),
            $this->onlyOnDetail ? $request->findModelOrFail()->getKey() : 'no-resource-id',
            md5($request->input('filter', 'no-filter'))
        );
    }

    /**
     * Get the displayable name of the metric.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        //
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name(), '-', null);
    }

    /**
     * Set whether the metric should refresh when actions are run.
     *
     * @param  bool  $value
     * @return $this
     */
    public function refreshWhenActionsRun($value = true)
    {
        $this->refreshWhenActionRuns = $value;

        return $this;
    }

    /**
     * Set whether the metric should refresh when actions are run.
     *
     * @param  bool  $value
     * @return $this
     *
     * @deprecated Use "refreshWhenActionsRun"
     */
    public function refreshWhenActionRuns($value = true)
    {
        return $this->refreshWhenActionsRun($value);
    }

    /**
     * Set whether the metric should refresh when filter changed.
     *
     * @param  bool  $value
     * @return $this
     */
    public function refreshWhenFiltersChange($value = true)
    {
        $this->refreshWhenFiltersChange = $value;

        return $this;
    }

    /**
     * Prepare the metric for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'class' => get_class($this),
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
            'helpWidth' => $this->getHelpWidth(),
            'helpText' => $this->getHelpText(),
            'refreshWhenActionRuns' => $this->refreshWhenActionRuns,
            'refreshWhenFiltersChange' => $this->refreshWhenFiltersChange,
        ]);
    }

    /**
     * Convert datetime to application timezone.
     *
     * @param  \Carbon\CarbonInterface  $datetime
     * @return \Carbon\CarbonInterface
     */
    protected function asQueryDatetime($datetime)
    {
        if (! $datetime instanceof \DateTimeImmutable) {
            return $datetime->copy()->timezone(config('app.timezone'));
        }

        return $datetime->timezone(config('app.timezone'));
    }

    /**
     * Format date between.
     *
     * @param  array  $ranges
     * @return array
     */
    protected function formatQueryDateBetween(array $ranges)
    {
        return array_map(function ($datetime) {
            return $this->asQueryDatetime($datetime);
        }, $ranges);
    }
}
