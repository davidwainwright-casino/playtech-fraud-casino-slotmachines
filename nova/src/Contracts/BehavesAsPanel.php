<?php

namespace Laravel\Nova\Contracts;

interface BehavesAsPanel
{
    /**
     * Make current field behaves as panel.
     *
     * @return \Laravel\Nova\Panel
     */
    public function asPanel();
}
