<?php

namespace Laravel\Nova\Actions;

use Illuminate\Support\Collection;

/**
 * @template TKey of array-key
 * @template TValue of \Laravel\Nova\Actions\Action
 *
 * @extends \Illuminate\Support\Collection<TKey, TValue>
 */
class ActionCollection extends Collection
{
    /**
     * Return action counts by type on index.
     *
     * @return array{standalone: mixed, resource: mixed}
     */
    public function countsByTypeOnIndex()
    {
        [$standalone, $resource] = $this->filter->shownOnIndex()->partition->isStandalone();

        return [
            'standalone' => $standalone->count(),
            'resource' => $resource->count(),
        ];
    }
}
