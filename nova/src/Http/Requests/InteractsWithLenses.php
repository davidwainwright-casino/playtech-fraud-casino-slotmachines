<?php

namespace Laravel\Nova\Http\Requests;

/**
 * @property-read string|null $lens
 */
trait InteractsWithLenses
{
    /**
     * Get the lens instance for the given request.
     *
     * @return \Laravel\Nova\Lenses\Lens
     */
    public function lens()
    {
        return $this->availableLenses()->first(function ($lens) {
            return $this->lens === $lens->uriKey();
        }) ?: abort($this->lensExists() ? 403 : 404);
    }

    /**
     * Get all of the possible lenses for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableLenses()
    {
        return transform($this->newResource(), function ($resource) {
            abort_unless($resource::authorizedToViewAny($this), 403);

            return $resource->availableLenses($this);
        });
    }

    /**
     * Determine if the specified action exists at all.
     *
     * @return bool
     */
    protected function lensExists()
    {
        return $this->newResource()->resolveLenses($this)->contains(function ($lens) {
            return $this->lens === $lens->uriKey();
        });
    }
}
