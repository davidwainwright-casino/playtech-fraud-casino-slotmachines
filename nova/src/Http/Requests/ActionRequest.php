<?php

namespace Laravel\Nova\Http\Requests;

use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Fluent;
use Laravel\Nova\Actions\ActionModelCollection;
use Laravel\Nova\Fields\ActionFields;

/**
 * @property-read string|null $resources
 * @property-read string|null $pivotAction
 */
class ActionRequest extends NovaRequest
{
    use QueriesResources;

    /**
     * Get the action instance specified by the request.
     *
     * @return \Laravel\Nova\Actions\Action|\Laravel\Nova\Actions\DestructiveAction
     */
    public function action()
    {
        return once(function () {
            $hasResources = ! empty($this->resources);

            return $this->availableActions()
                        ->filter(function ($action) use ($hasResources) {
                            return $hasResources ? true : $action->isStandalone();
                        })->first(function ($action) {
                            return $action->uriKey() == $this->query('action');
                        }) ?: abort($this->actionExists() ? 403 : 404);
        });
    }

    /**
     * Get the all actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function resolveActions()
    {
        return $this->isPivotAction()
                    ? $this->newResource()->resolvePivotActions($this)
                    : $this->newResource()->resolveActions($this);
    }

    /**
     * Get the possible actions for the request.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function availableActions()
    {
        return $this->resolveActions()->filter->authorizedToSee($this)->values();
    }

    /**
     * Determine if the specified action exists at all.
     *
     * @return bool
     */
    protected function actionExists()
    {
        return $this->resolveActions()->contains(function ($action) {
            return $action->uriKey() == $this->query('action');
        });
    }

    /**
     * Determine if the action being executed is a pivot action.
     *
     * @return bool
     */
    public function isPivotAction()
    {
        return $this->pivotAction === 'true';
    }

    /**
     * Get the selected models for the action in chunks.
     *
     * @param  int  $count
     * @param  \Closure(\Laravel\Nova\Actions\ActionModelCollection):mixed  $callback
     * @return mixed
     */
    public function chunks($count, Closure $callback)
    {
        $output = [];

        $this->toSelectedResourceQuery()
            ->cursor()
            ->chunk($count)
            ->each(function ($chunk) use ($callback, &$output) {
                $output[] = $callback($this->mapChunk($chunk));
            });

        return $output;
    }

    /**
     * Get the query for the models that were selected by the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function toSelectedResourceQuery()
    {
        if ($this->allResourcesSelected()) {
            return $this->toQuery();
        }

        $query = $this->viaRelationship()
                    ? $this->modelsViaRelationship()
                    : tap($this->newQueryWithoutScopes(), function ($query) {
                        $resource = $this->resource();

                        $resource::indexQuery(
                            $this, $query->with($resource::$with)
                        );
                    });

        return $query->tap(function ($query) {
            $query->whereKey(explode(',', $this->resources))
                ->latest($this->model()->getQualifiedKeyName());
        });
    }

    /**
     * Get the query for the related models that were selected by the user.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function modelsViaRelationship()
    {
        return tap($this->findParentResource(), function ($resource) {
            abort_unless($resource->hasRelatableField($this, $this->viaRelationship), 404);
        })->model()->{$this->viaRelationship}()
                        ->withoutGlobalScopes()
                        ->whereIn($this->model()->getQualifiedKeyName(), explode(',', $this->resources));
    }

    /**
     * Map the chunk of models into an appropriate state.
     *
     * @param  \Illuminate\Support\LazyCollection|\Illuminate\Database\Eloquent\Collection  $chunk
     * @return \Laravel\Nova\Actions\ActionModelCollection
     */
    protected function mapChunk($chunk)
    {
        return ActionModelCollection::make($this->isPivotAction()
                    ? $chunk->map->pivot
                    : $chunk);
    }

    /**
     * Validate the given fields.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateFields()
    {
        $this->action()->validateFields($this);
    }

    /**
     * Resolve the fields for database storage using the request.
     *
     * @return array
     */
    public function resolveFieldsForStorage()
    {
        return collect($this->resolveFields()->getAttributes())->map(function ($attribute) {
            return $attribute instanceof UploadedFile ? $attribute->hashName() : $attribute;
        })->all();
    }

    /**
     * Resolve the fields using the request.
     *
     * @return \Laravel\Nova\Fields\ActionFields
     */
    public function resolveFields()
    {
        return once(function () {
            $fields = new Fluent;

            $results = collect($this->action()->fields($this))
                            ->filter->authorizedToSee($this)
                            ->mapWithKeys(function ($field) use ($fields) {
                                return [$field->attribute => $field->fillForAction($this, $fields)];
                            });

            return new ActionFields(collect($fields->getAttributes()), $results->filter(function ($field) {
                return is_callable($field);
            }));
        });
    }

    /**
     * Get the key of model that lists the action on its dashboard.
     *
     * When running pivot actions, this is the key of the owning model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return int
     */
    public function actionableKey($model)
    {
        return $this->isPivotAction()
                        ? $model->{$this->pivotRelation()->getForeignPivotKeyName()}
                        : $model->getKey();
    }

    /**
     * Get the model instance that lists the action on its dashboard.
     *
     * When running pivot actions, this is the owning model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function actionableModel()
    {
        return $this->isPivotAction()
                        ? $this->newViaResource()->model()
                        : $this->model();
    }

    /**
     * Get the key of model that is the target of the action.
     *
     * When running pivot actions, this is the key of the target model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return int
     */
    public function targetKey($model)
    {
        return $this->isPivotAction()
                        ? $model->{$this->pivotRelation()->getRelatedPivotKeyName()}
                        : $model->getKey();
    }

    /**
     * Get an instance of the target model of the action.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function targetModel()
    {
        return $this->isPivotAction() ? $this->pivotRelation()->newPivot() : $this->model();
    }

    /**
     * Get the many-to-many relationship for a pivot action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany|\Illuminate\Database\Eloquent\Relations\BelongsToMany|null
     */
    public function pivotRelation()
    {
        if ($this->isPivotAction()) {
            return tap($this->newViaResource(), function ($resource) {
                abort_unless($resource->hasRelatableField($this, $this->viaRelationship), 404);
            })->model()->{$this->viaRelationship}();
        }
    }

    /**
     * Determine if this request is an action request.
     *
     * @return bool
     */
    public function isActionRequest()
    {
        return true;
    }
}
