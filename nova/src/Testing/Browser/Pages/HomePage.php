<?php

namespace Laravel\Nova\Testing\Browser\Pages;

class HomePage extends Page
{
    /**
     * Create a new page instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('/');
    }
}
