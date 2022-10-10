<?php

namespace Laravel\Nova\Testing\Browser\Pages;

class Replicate extends Create
{
    public $fromResourceId;

    /**
     * Create a new page instance.
     *
     * @param  string  $resourceName
     * @param  int|string  $fromResourceId
     * @param  array  $queryParams
     * @return void
     */
    public function __construct($resourceName, $fromResourceId, $queryParams = [])
    {
        parent::__construct($resourceName, $queryParams);

        $this->fromResourceId = $fromResourceId;

        $this->setNovaPage("/resources/{$this->resourceName}/{$this->fromResourceId}/replicate");
    }
}
