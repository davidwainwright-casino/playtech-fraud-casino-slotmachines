<?php

namespace Laravel\Nova\Tools;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Dashboard extends Tool
{
    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function menu(Request $request)
    {
        $dashboards = collect(Nova::availableDashboards($request));

        if ($dashboards->count() > 1) {
            return MenuSection::make(Nova::__('Dashboards'), $dashboards->map(function ($dashboard) use ($request) {
                return $dashboard->menu($request);
            }))
            ->collapsable()
            ->icon('view-grid');
        }

        if ($dashboards->count() == 1) {
            return MenuSection::make($dashboards->first()->label(), $dashboards)
                ->path("/dashboards/{$dashboards->first()->uriKey()}")
                ->icon('view-grid');
        }
    }
}
