<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;

trait DeterminesIfCreateRelationCanBeShown
{
    /**
     * The callback used to determine if the create relation button should be shown.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest):bool)|bool
     */
    public $showCreateRelationButtonCallback;

    /**
     * Set the callback used to determine if the field is required.
     *
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest):bool)|bool  $callback
     * @return $this
     */
    public function showCreateRelationButton($callback = true)
    {
        $this->showCreateRelationButtonCallback = $callback;

        return $this;
    }

    /**
     * Hide the create relation button from forms.
     *
     * @return $this
     */
    public function hideCreateRelationButton()
    {
        $this->showCreateRelationButtonCallback = false;

        return $this;
    }

    /**
     * Determine if Nova should show the edit pivot relation button.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function createRelationShouldBeShown(NovaRequest $request)
    {
        return with($this->showCreateRelationButtonCallback, function ($callback) use ($request) {
            if ($callback === true || (is_callable($callback) && call_user_func($callback, $request))) {
                return true;
            }

            return false;
        });
    }
}
