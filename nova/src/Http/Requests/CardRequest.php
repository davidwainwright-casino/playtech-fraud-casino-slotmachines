<?php

namespace Laravel\Nova\Http\Requests;

class CardRequest extends NovaRequest
{
    /**
     * Get all of the possible metrics for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    public function availableCards()
    {
        $resource = $this->newResource();

        if ($this->resourceId) {
            return $this->newResource()->availableCardsForDetail($this);
        }

        return $this->newResource()->availableCards($this);
    }
}
