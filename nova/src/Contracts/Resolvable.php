<?php

namespace Laravel\Nova\Contracts;

/**
 * @property bool $pivot
 * @property string|null $pivotAccessor
 * @property \Illuminate\Database\Eloquent\Relations\MorphToMany|\Illuminate\Database\Eloquent\Relations\BelongsToMany|null $pivotRelation
 */
interface Resolvable
{
    /**
     * Resolve the element's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null);

    /**
     * Resolve the field's value for display.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolveForDisplay($resource, $attribute = null);
}
