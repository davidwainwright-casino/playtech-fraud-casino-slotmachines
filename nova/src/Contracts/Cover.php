<?php

namespace Laravel\Nova\Contracts;

interface Cover
{
    /**
     * Resolve the thumbnail URL for the field.
     *
     * @return string|null
     */
    public function resolveThumbnailUrl();

    /**
     * Determine whether the field should have rounded corners.
     *
     * @return bool
     */
    public function isRounded();

    /**
     * Determine whether the field should have squared corners.
     *
     * @return bool
     */
    public function isSquared();
}
