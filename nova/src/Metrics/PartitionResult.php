<?php

namespace Laravel\Nova\Metrics;

use Closure;
use JsonSerializable;

class PartitionResult implements JsonSerializable
{
    use RoundingPrecision;

    /**
     * The value of the result.
     *
     * @var array<string, int|float>
     */
    public $value;

    /**
     * The custom label name.
     *
     * @var array<string, string>
     */
    public $labels = [];

    /**
     * The custom label colors.
     *
     * @var \Laravel\Nova\Metrics\PartitionColors
     */
    public $colors;

    /**
     * Create a new partition result instance.
     *
     * @param  array<string, int|float>  $value
     * @return void
     */
    public function __construct(array $value)
    {
        $this->value = $value;
        $this->colors = new PartitionColors();
    }

    /**
     * Format the labels for the partition result.
     *
     * @param  \Closure(string):string  $callback
     * @return $this
     */
    public function label(Closure $callback)
    {
        $this->labels = collect($this->value)->mapWithKeys(function ($value, $label) use ($callback) {
            return [$label => $callback($label)];
        })->all();

        return $this;
    }

    /**
     * Set the custom label colors.
     *
     * @param  array<string, string>  $colors
     * @return $this
     */
    public function colors(array $colors)
    {
        $this->colors = new PartitionColors($colors);

        return $this;
    }

    /**
     * Prepare the metric result for JSON serialization.
     *
     * @return array<string, array<array-key, array<string, mixed>>>
     */
    public function jsonSerialize(): array
    {
        $values = collect($this->value);
        $total = $values->sum();

        return [
            'value' => $values->map(function ($value, $label) use ($total) {
                $resolvedLabel = $this->labels[$label] ?? $label;

                return array_filter([
                    'color' => data_get($this->colors->colors, $label, $this->colors->get($resolvedLabel)),
                    'label' => $resolvedLabel,
                    'value' => $value,
                    'percentage' => $total > 0 ? round(($value / $total) * 100, $this->roundingPrecision, $this->roundingMode) : 0,
                ], function ($value) {
                    return ! is_null($value);
                });
            })->values()->all(),
        ];
    }
}
