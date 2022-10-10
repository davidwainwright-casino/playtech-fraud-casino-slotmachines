<?php

namespace Laravel\Nova;

class TrashedStatus
{
    const DEFAULT = '';

    const WITH = 'with';

    const ONLY = 'only';

    /**
     * Get trashed status from boolean.
     *
     * @param  bool  $withTrashed
     * @return string
     */
    public static function fromBoolean($withTrashed)
    {
        return $withTrashed ? self::WITH : self::DEFAULT;
    }
}
