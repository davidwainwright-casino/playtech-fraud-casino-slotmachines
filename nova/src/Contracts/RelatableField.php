<?php

namespace Laravel\Nova\Contracts;

/**
 * @mixin \Laravel\Nova\Fields\Field
 *
 * @property string $attribute
 * @property \Laravel\Nova\Resource $resourceClass
 * @property string $resourceName
 */
interface RelatableField
{
    /**
     * Get the relationship name.
     *
     * @return string
     */
    public function relationshipName();

    /**
     * Get the relationship type.
     *
     * @return string
     */
    public function relationshipType();
}
