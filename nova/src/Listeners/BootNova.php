<?php

namespace Laravel\Nova\Listeners;

use Laravel\Nova\Nova;
use Laravel\Nova\NovaServiceProvider;
use Laravel\Nova\Tools\Dashboard;
use Laravel\Nova\Tools\ResourceManager;

class BootNova
{
    /**
     * Handle the event.
     *
     * @param  mixed  $event
     * @return void
     */
    public function handle($event)
    {
        if (! app()->providerIsLoaded(NovaServiceProvider::class)) {
            app()->register(NovaServiceProvider::class);
        }

        $this->registerTools();
        $this->registerResources();
    }

    /**
     * Boot the standard Nova resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        Nova::resources([
            Nova::actionResource(),
        ]);
    }

    /**
     * Boot the standard Nova tools.
     *
     * @return void
     */
    protected function registerTools()
    {
        Nova::tools([
            new Dashboard,
            new ResourceManager,
        ]);
    }
}
