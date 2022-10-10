<?php

namespace Laravel\Nova\Fields;

class MorphMany extends HasMany
{
    /**
     * Get the relationship type.
     *
     * @return string
     */
    public function relationshipType()
    {
        return 'morphMany';
    }
}
