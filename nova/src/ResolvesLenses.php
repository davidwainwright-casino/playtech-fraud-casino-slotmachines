<?php

namespace Laravel\Nova;

use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesLenses
{
    /**
     * Get the lenses that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Lenses\Lens>
     */
    public function availableLenses(NovaRequest $request)
    {
        return $this->resolveLenses($request)->filter->authorizedToSee($request)->values();
    }

    /**
     * Get the lenses for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Lenses\Lens>
     */
    public function resolveLenses(NovaRequest $request)
    {
        return collect(array_values($this->filter($this->lenses($request))));
    }

    /**
     * Get the lenses available on the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }
}
