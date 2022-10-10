<?php

namespace Laravel\Nova\Metrics;

use JsonSerializable;

class TrendResult implements JsonSerializable
{
    use TransformsResults;

    /**
     * The value of the result.
     *
     * @var int|float|numeric-string|null
     */
    public $value;

    /**
     * The trend data of the result.
     *
     * @var array<string, int|float|numeric-string|null>
     */
    public $trend = [];

    /**
     * The metric value prefix.
     *
     * @var string
     */
    public $prefix;

    /**
     * The metric value suffix.
     *
     * @var string
     */
    public $suffix;

    /**
     * Whether to run inflection on the suffix.
     *
     * @var bool
     */
    public $suffixInflection = true;

    /**
     * The metric value formatting.
     *
     * @var string
     */
    public $format;

    /**
     * Create a new trend result instance.
     *
     * @param  int|float|numeric-string|null  $value
     * @return void
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * Set the primary result amount for the trend.
     *
     * @param  int|float|numeric-string|null  $value
     * @return $this
     */
    public function result($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the latest value of the trend as the primary result.
     *
     * @return $this
     */
    public function showLatestValue()
    {
        return $this->result(last($this->trend));
    }

    /**
     * Set the sum of all the values of the trend as the primary result.
     *
     * @return $this
     */
    public function showSumValue()
    {
        return $this->result(array_sum(array_values($this->trend)));
    }

    /**
     * Set the trend of data for the metric.
     *
     * @param  array<string, int|float|numeric-string|null>  $trend
     * @return $this
     */
    public function trend(array $trend)
    {
        $this->trend = $trend;

        return $this;
    }

    /**
     * Indicate that the metric represents a dollar value.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function dollars($symbol = '$')
    {
        return $this->prefix($symbol);
    }

    /**
     * Indicate that the metric represents a euro value.
     *
     * @param  string  $symbol
     * @return $this
     */
    public function euros($symbol = '€')
    {
        return $this->prefix($symbol);
    }

    /**
     * Set the metric value prefix.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Set the metric value suffix.
     *
     * @param  string  $suffix
     * @return $this
     */
    public function suffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Don't apply suffix inflections.
     *
     * @return $this
     */
    public function withoutSuffixInflection()
    {
        $this->suffixInflection = false;

        return $this;
    }

    /**
     * Set the metric value formatting.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Prepare the metric result for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->resolveTransformedValue($this->value),
            'trend' => collect($this->trend)->transform(function ($value) {
                return $this->resolveTransformedValue($value);
            })->all(),
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'suffixInflection' => $this->suffixInflection,
            'format' => $this->format,
        ];
    }
}
