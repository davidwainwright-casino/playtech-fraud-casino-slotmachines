<?php

namespace Laravel\Nova\Concerns;

use Laravel\Nova\Actions\ActionResource;

trait InteractsWithActionEvent
{
    /**
     * Get the configured ActionResource class.
     *
     * @return class-string<\Laravel\Nova\Actions\ActionResource>
     */
    public static function actionResource()
    {
        return config('nova.actions.resource') ?? ActionResource::class;
    }

    /**
     * Get a new instance of the configured ActionEvent.
     *
     * @return \Illuminate\Database\Eloquent\Model|\Laravel\Nova\Actions\ActionEvent
     */
    public static function actionEvent()
    {
        return static::actionResource()::newModel();
    }

    /**
     * Invoke the callback with an instance of the configured ActionEvent if it is available.
     *
     * @param  callable(\Laravel\Nova\Actions\ActionEvent):mixed  $callback
     * @return mixed
     */
    public static function usingActionEvent(callable $callback)
    {
        if (! is_null(config('nova.actions.resource'))) {
            return call_user_func($callback, static::actionEvent());
        }
    }
}
