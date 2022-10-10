<?php

namespace Laravel\Nova\Dashboards;

use Illuminate\Support\Str;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboard;
use Laravel\Nova\Nova;

class Main extends Dashboard
{
    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return class_basename($this);
    }

    /**
     * Get the URI key of the dashboard.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::snake(class_basename($this));
    }

    /**
     * Get the cards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    public function cards()
    {
        return [
            new Help,
        ];
    }
}
