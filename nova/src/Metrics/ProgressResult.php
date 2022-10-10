<?php

namespace Laravel\Nova\Metrics;

use JsonSerializable;

class ProgressResult implements JsonSerializable
{
    use RoundingPrecision;
    use TransformsResults;

    /**
     * The current value of the result.
     *
     * @var int|float
     */
    public $value;

    /**
     * The target value.
     *
     * @var int|float
     */
    public $target;

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
     * Indicates if this metric is to be avoided.
     *
     * @var bool
     */
    public $avoid = false;

    /**
     * Create a new progress result instance.
     *
     * @param  int|float  $value
     * @param  int|float  $target
     * @return void
     */
    public function __construct($value, $target)
    {
        $this->value = $value;
        $this->target = $target;

        $this->roundingPrecision = 2;
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
     * Indicates that this progress metric is tracking a "goal" that should be avoided.
     *
     * @return $this
     */
    public function avoid()
    {
        $this->avoid = true;

        return $this;
    }

    /**
     * Prepare the metric result for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $target = max($this->value, $this->target);

        return [
            'value' => $this->resolveTransformedValue($this->value),
            'target' => $this->resolveTransformedValue($target),
            'percentage' => round(($this->value / $target) * 100, $this->roundingPrecision, $this->roundingMode),
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'suffixInflection' => $this->suffixInflection,
            'format' => $this->format,
            'avoid' => $this->avoid,
        ];
    }
}
