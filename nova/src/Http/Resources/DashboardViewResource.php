<?php

namespace Laravel\Nova\Http\Resources;

use Laravel\Nova\Http\Requests\DashboardRequest;
use Laravel\Nova\Nova;

class DashboardViewResource extends Resource
{
    /**
     * The dashboard name.
     *
     * @var string
     */
    protected $name;

    /**
     * Construct a new Dashboard Resource.
     *
     * @param  string  $name
     * @return void
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardRequest  $request
     * @return array
     */
    public function toArray($request)
    {
        $dashboard = $this->authorizedDashboardForRequest($request);

        return [
            'label' => $dashboard->label(),
            'cards' => $request->availableCards($this->name),
        ];
    }

    /**
     * Get authorized dashboard for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardRequest  $request
     * @return \Laravel\Nova\Dashboard
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function authorizedDashboardForRequest(DashboardRequest $request)
    {
        return tap(Nova::dashboardForKey($this->name, $request), function ($dashboard) {
            abort_if(is_null($dashboard), 404);
        });
    }
}
