<?php

namespace Laravel\Nova\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class ResourceMissingException extends Exception
{
    /**
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    public function __construct(Model $model)
    {
        parent::__construct(
            __('Unable to find Resource for model [:model].', ['model' => get_class($model)])
        );
    }
}
