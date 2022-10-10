<?php

namespace Wainwright\CasinoDog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Wainwright\CasinoDog\CasinoDog
 */
class CasinoDog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Wainwright\CasinoDog\CasinoDog::class;
    }
}
