<?php

namespace Laravel\Nova\Actions;

use Illuminate\Support\Arr;

class Response
{
    /**
     * Determine if action was executed.
     *
     * @var bool
     */
    public $wasExecuted = false;

    /**
     * List of action results.
     *
     * @var array
     */
    public $results = [];

    /**
     * Mark response as successful.
     *
     * @param  array|mixed|null  $results
     * @return $this
     */
    public function successful($results = null)
    {
        $this->wasExecuted = true;
        $this->results = Arr::wrap($results ?? []);

        return $this;
    }

    /**
     * Mark response as failed.
     *
     * @return $this
     */
    public function failed()
    {
        $this->wasExecuted = false;

        return $this;
    }
}
