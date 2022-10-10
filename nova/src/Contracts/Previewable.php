<?php

namespace Laravel\Nova\Contracts;

/**
 * @mixin \Laravel\Nova\Fields\Field
 */
interface Previewable
{
    /**
     * Return a preview for the given field value.
     *
     * @param  string  $value
     * @return mixed
     */
    public function previewFor($value);
}
