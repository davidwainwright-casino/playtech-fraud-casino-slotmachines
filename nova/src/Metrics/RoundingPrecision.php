<?php

namespace Laravel\Nova\Metrics;

trait RoundingPrecision
{
    /**
     * Rounding precision.
     *
     * @var int
     */
    public $roundingPrecision = 0;

    /**
     * Rounding mode.
     *
     * @var 1|2|3|4
     */
    public $roundingMode = PHP_ROUND_HALF_UP;

    /**
     * Set the precision level used when rounding the value.
     *
     * @param  int  $precision
     * @param  1|2|3|4  $mode
     * @return $this
     */
    public function precision($precision = 0, $mode = PHP_ROUND_HALF_UP)
    {
        $this->roundingPrecision = $precision;

        if (in_array($mode, [PHP_ROUND_HALF_UP, PHP_ROUND_HALF_DOWN, PHP_ROUND_HALF_EVEN, PHP_ROUND_HALF_ODD])) {
            $this->roundingMode = $mode;
        }

        return $this;
    }
}
