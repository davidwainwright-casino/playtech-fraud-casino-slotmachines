<?php

namespace Laravel\Nova\Metrics;

use Laravel\SerializableClosure\SerializableClosure;

trait TransformsResults
{
    /**
     * The callback used to transform the value before display.
     *
     * @var \Closure|callable|null
     */
    public $transformCallback;

    /**
     * Set the callback used to transform the value before presentation.
     *
     * @param  \Closure|callable  $transformCallback
     * @return $this
     */
    public function transform($transformCallback)
    {
        $this->transformCallback = new SerializableClosure($transformCallback);

        return $this;
    }

    /**
     * Resolve the transformed value result.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function resolveTransformedValue($value)
    {
        return transform($value, $this->transformCallback ?? function ($value) {
            return $value;
        });
    }
}
