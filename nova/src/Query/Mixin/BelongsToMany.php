<?php

namespace Laravel\Nova\Query\Mixin;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

class BelongsToMany
{
    /**
     * Get default pivot attributes using mixin.
     *
     * @return \Closure
     */
    public function getDefaultPivotAttributes()
    {
        return function () {
            return collect($this->pivotValues)->mapWithKeys(function ($pivot) {
                return [$pivot['column'] => $pivot['value']];
            })->all();
        };
    }

    /**
     * Apply default pivot query using mixin.
     *
     * @return \Closure
     */
    public function applyDefaultPivotQuery()
    {
        return function ($query) {
            $query->from($this->table);

            if ($this instanceof MorphToMany) {
                $query->where($this->qualifyPivotColumn($this->morphType), $this->morphClass);
            }

            foreach ($this->pivotWheres as $arguments) {
                $query->where(...$arguments);
            }

            foreach ($this->pivotWhereIns as $arguments) {
                $query->whereIn(...$arguments);
            }

            foreach ($this->pivotWhereNulls as $arguments) {
                $query->whereNull(...$arguments);
            }

            return $query->where($this->getQualifiedForeignPivotKeyName(), $this->parent->{$this->parentKey});
        };
    }
}
