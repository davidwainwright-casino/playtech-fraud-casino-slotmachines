<?php

namespace Laravel\Nova\Http\Requests;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Laravel\Nova\Contracts\RelatableField;

/**
 * @property-read string|null $orderBy
 * @property-read string|null $orderByDirection
 */
class LensRequest extends NovaRequest
{
    use DecodesFilters, InteractsWithLenses;

    /**
     * Whether to include the table order prefix.
     *
     * @var bool
     */
    protected $tableOrderPrefix = true;

    /**
     * Apply the specified filters to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function withFilters($query)
    {
        return $this->filter($query);
    }

    /**
     * Apply the specified filters to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter($query)
    {
        $this->filters()->each->__invoke($this, $query);

        return $query;
    }

    /**
     * Apply the specified ordering to the given query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function withOrdering($query)
    {
        if (! $this->orderBy || ! $this->orderByDirection) {
            return $query;
        }

        $model = $this->model();

        $fieldExists = $this->lens()->availableFields($this)
            ->transform(function ($field) use ($model) {
                return $field instanceof RelatableField
                    ? $this->getRelationForeignKeyName($model->{$field->attribute}())
                    : $field->attribute ?? null;
            })->filter()
            ->first(function ($attribute) {
                return $attribute == $this->orderBy;
            });

        if ($fieldExists) {
            return $query->orderBy(
                ($this->tableOrderPrefix ? $query->getModel()->getTable().'.' : '').$this->orderBy,
                $this->orderByDirection === 'asc' ? 'asc' : 'desc'
            );
        }

        return $query;
    }

    /**
     * Disable prepending of the table order.
     *
     * @return $this
     */
    public function withoutTableOrderPrefix()
    {
        $this->tableOrderPrefix = false;

        return $this;
    }

    /**
     * Get all of the possibly available filters for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableFilters()
    {
        return $this->lens()->availableFilters($this);
    }

    /**
     * Map the given models to the appropriate resource for the request.
     *
     * @param  \Illuminate\Support\Collection  $models
     * @return \Illuminate\Support\Collection
     */
    public function toResources(Collection $models)
    {
        $resource = $this->resource();

        return $models->map(function ($model) use ($resource) {
            $lensResource = $this->lens()->setResource($model);

            return transform((new $resource($model))->serializeForIndex(
                $this, $lensResource->resolveFields($this)
            ), function ($payload) use ($model, $lensResource) {
                $payload['actions'] = collect(array_values($lensResource->actions($this)))
                        ->filter(function ($action) {
                            return $action->shownOnIndex() || $action->shownOnTableRow();
                        })
                        ->filter->authorizedToSee($this)
                        ->filter->authorizedToRun($this, $model)
                        ->values();

                return $payload;
            });
        });
    }

    /**
     * Get foreign key name for relation.
     *
     * @param  \Illuminate\Database\Eloquent\Relations\Relation  $relation
     * @return string
     */
    protected function getRelationForeignKeyName(Relation $relation)
    {
        return method_exists($relation, 'getForeignKeyName')
            ? $relation->getForeignKeyName()
            : $relation->getForeignKey();
    }

    /**
     * Get per page.
     *
     * @return int
     */
    public function perPage()
    {
        $resource = $this->resource();

        $perPageOptions = $resource::perPageOptions();

        if (empty($perPageOptions)) {
            $perPageOptions = [$resource::newModel()->getPerPage()];
        }

        return (int) in_array($this->perPage, $perPageOptions) ? $this->perPage : $perPageOptions[0];
    }

    /**
     * Determine if this request is an action request.
     *
     * @return bool
     */
    public function isActionRequest()
    {
        return $this->segment(5) == 'actions';
    }
}
