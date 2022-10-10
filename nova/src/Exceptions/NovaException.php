<?php

namespace Laravel\Nova\Exceptions;

use Exception;

class NovaException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param  string  $method
     * @param  string  $class
     * @return \Laravel\Nova\Exceptions\NovaException
     */
    public static function helperNotSupported($method, $class)
    {
        return new static("The {$method} helper method is not supported by the {$class} class.");
    }
}
