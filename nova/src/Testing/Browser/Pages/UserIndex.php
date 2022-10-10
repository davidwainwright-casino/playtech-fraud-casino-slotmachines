<?php

namespace Laravel\Nova\Testing\Browser\Pages;

class UserIndex extends Index
{
    /**
     * Create a new page instance.
     *
     * @param  array  $queryParams
     * @return void
     */
    public function __construct($queryParams = [])
    {
        parent::__construct('users', $queryParams);
    }
}
