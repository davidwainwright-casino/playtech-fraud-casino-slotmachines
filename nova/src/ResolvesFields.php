<?php

namespace Laravel\Nova;

use Closure;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Contracts\BehavesAsPanel;
use Laravel\Nova\Contracts\Cover;
use Laravel\Nova\Contracts\Deletable;
use Laravel\Nova\Contracts\Downloadable;
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Contracts\Resolvable;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesFields
{
    /**
     * Resolve the index fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function indexFields(NovaRequest $request)
    {
        return $this->availableFields($request)
            ->when($request->viaRelationship(), $this->relatedFieldResolverCallback($request))
            ->filterForIndex($request, $this->resource)
            ->withoutListableFields()
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the detail fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function detailFields(NovaRequest $request)
    {
        return $this->availableFields($request)
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->when($this->shouldAddActionsField($request), function ($fields) {
                return $fields->push($this->actionfield());
            })
            ->filterForDetail($request, $this->resource)
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the preview fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function previewFields(NovaRequest $request)
    {
        return $this->buildAvailableFields($request, ['fieldsForDetail'])
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->withoutResourceTools()
            ->withoutListableFields()
            ->filterForPreview($request, $this->resource)
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Return the count of preview fields available.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return int
     */
    public function previewFieldsCount(NovaRequest $request)
    {
        return $this->buildAvailableFields($request, ['fieldsForDetail'])
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->withoutResourceTools()
            ->withoutListableFields()
            ->filterForPreview($request, $this->resource)
            ->authorized($request)
            ->count();
    }

    /**
     * Resolve the deletable fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field&\Laravel\Nova\Contracts\Deletable>
     */
    public function deletableFields(NovaRequest $request)
    {
        return $this->availableFieldsOnIndexOrDetail($request)
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->whereInstanceOf(Deletable::class)
            ->unique(function ($field) {
                return $field->attribute;
            })
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the downloadable fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field&\Laravel\Nova\Contracts\Downloadable>
     */
    public function downloadableFields(NovaRequest $request)
    {
        return $this->availableFieldsOnIndexOrDetail($request)
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->whereInstanceOf(Downloadable::class)
            ->unique(function ($field) {
                return $field->attribute;
            })
            ->authorized($request)
            ->resolveForDisplay($this->resource);
    }

    /**
     * Resolve the filterable fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function filterableFields(NovaRequest $request)
    {
        return $this->availableFieldsOnIndexOrDetail($request)
            ->when($request->viaRelationship(), function ($fields) use ($request) {
                $relatedField = $request->findParentResource()->relatableField($request, $request->viaRelationship);

                if (! is_null($relatedField)) {
                    $fields->prepend($relatedField);
                }

                return call_user_func($this->relatedFieldResolverCallback($request), $fields);
            })
            ->withOnlyFilterableFields()
            ->unique(function ($field) {
                return $field->attribute;
            })
            ->authorized($request);
    }

    /**
     * Get related field from resource by attribute.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $attribute
     * @return \Laravel\Nova\Fields\Field|null
     */
    public function relatableField(NovaRequest $request, $attribute)
    {
        return $this->availableFieldsOnIndexOrDetail($request)
            ->when($request->viaRelationship(), $this->fieldResolverCallback($request))
            ->whereInstanceOf(RelatableField::class)
            ->when($this->shouldAddActionsField($request), function ($fields) {
                return $fields->push($this->actionfield());
            })
            ->first(function ($field) use ($attribute) {
                return $field->attribute === $attribute;
            });
    }

    /**
     * Determine resource has relatable field by attribute.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $attribute
     * @return bool
     */
    public function hasRelatableField(NovaRequest $request, $attribute)
    {
        return $this->relatableField($request, $attribute) !== null;
    }

    /**
     * Determine if the resource should have an Action field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    protected function shouldAddActionsField($request)
    {
        return with($this->actionfield(), function ($actionField) use ($request) {
            return in_array(Actionable::class, class_uses_recursive(static::newModel())) && $actionField->authorizedToSee($request);
        });
    }

    /**
     * Return a new Action field instance.
     *
     * @return \Laravel\Nova\Fields\MorphMany
     */
    protected function actionfield()
    {
        return MorphMany::make(Nova::__('Actions'), 'actions', Nova::actionResource())
            ->canSee(function ($request) {
                return Nova::actionResource()::authorizedToViewAny($request);
            });
    }

    /**
     * Resolve the detail fields and assign them to their associated panel.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function detailFieldsWithinPanels(NovaRequest $request, Resource $resource)
    {
        return $this->detailFields($request)
            ->assignDefaultPanel(
                $request->viaRelationship() && $request->isResourceDetailRequest()
                    ? Panel::defaultNameForViaRelationship($resource, $request)
                    : Panel::defaultNameForDetail($resource)
            );
    }

    /**
     * Resolve the creation fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function creationFields(NovaRequest $request)
    {
        $fields = $this->removeNonCreationFields(
            $request,
            $this->availableFields($request)->authorized($request)
        )->resolve($this->resource);

        return $request->viaRelationship()
            ? $this->withPivotFields($request, $fields->all())
            : $fields;
    }

    /**
     * Return the creation fields excluding any readonly ones.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function creationFieldsWithoutReadonly(NovaRequest $request)
    {
        return $this->creationFields($request)
            ->withoutReadonly($request);
    }

    /**
     * Resolve the creation fields and assign them to their associated panel.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function creationFieldsWithinPanels(NovaRequest $request)
    {
        return $this->creationFields($request)
            ->assignDefaultPanel(Panel::defaultNameForCreate($request->newResource()));
    }

    /**
     * Resolve the creation pivot fields for a related resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $relatedResource
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function creationPivotFields(NovaRequest $request, $relatedResource)
    {
        return $this->removeNonCreationFields(
            $request,
            $this->resolvePivotFields($request, $relatedResource)
        );
    }

    /**
     * Remove non-creation fields from the given collection.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    protected function removeNonCreationFields(NovaRequest $request, FieldCollection $fields)
    {
        return $fields->reject(function ($field) use ($request) {
            return $field instanceof ListableField ||
                ($field instanceof ResourceTool || $field instanceof ResourceToolElement) ||
                $field->attribute === 'ComputedField' ||
                ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                ! $field->isShownOnCreation($request);
        });
    }

    /**
     * Resolve the update fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function updateFields(NovaRequest $request)
    {
        return $this->resolveFields($request, function ($fields) use ($request) {
            return $this->removeNonUpdateFields($request, $fields);
        });
    }

    /**
     * Return the update fields excluding any readonly ones.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function updateFieldsWithoutReadonly(NovaRequest $request)
    {
        return $this->updateFields($request)
            ->withoutReadonly($request);
    }

    /**
     * Resolve the update fields and assign them to their associated panel.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource|null  $resource
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function updateFieldsWithinPanels(NovaRequest $request, Resource $resource = null)
    {
        return $this->updateFields($request)
            ->assignDefaultPanel(Panel::defaultNameForUpdate($resource ?? $request->newResource()));
    }

    /**
     * Resolve the update pivot fields for a related resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $relatedResource
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function updatePivotFields(NovaRequest $request, $relatedResource)
    {
        return $this->removeNonUpdateFields(
            $request,
            $this->resolvePivotFields($request, $relatedResource)
        );
    }

    /**
     * Remove non-update fields from the given collection.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    protected function removeNonUpdateFields(NovaRequest $request, FieldCollection $fields)
    {
        return $fields->reject(function ($field) use ($request) {
            return $field instanceof ListableField ||
                ($field instanceof ResourceTool || $field instanceof ResourceToolElement) ||
                $field->attribute === 'ComputedField' ||
                ($field instanceof ID && $field->attribute === $this->resource->getKeyName()) ||
                ! $field->isShownOnUpdate($request, $this->resource);
        });
    }

    /**
     * Remove non-preview fields from the given collection.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Fields\FieldCollection  $fields
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    protected function removeNonPreviewFields(NovaRequest $request, FieldCollection $fields)
    {
        return $fields->reject(function ($field) {
            return $field instanceof ListableField ||
                ($field instanceof ResourceTool || $field instanceof ResourceToolElement) ||
                $field->attribute === 'ComputedField' ||
                ($field instanceof ID && $field->attribute === $this->resource->getKeyName());
        });
    }

    /**
     * Resolve the given fields to their values.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  (\Closure(\Laravel\Nova\Fields\FieldCollection):\Laravel\Nova\Fields\FieldCollection)|null  $filter
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    protected function resolveFields(NovaRequest $request, Closure $filter = null)
    {
        $fields = $this->availableFields($request)->authorized($request);

        if (! is_null($filter)) {
            $fields = $filter($fields);
        }

        $fields->resolve($this->resource);

        return $request->viaRelationship()
            ? $this->withPivotFields($request, $fields->all())
            : $fields;
    }

    /**
     * Resolve the non pivot fields for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     *
     * @deprecated 4.x
     */
    protected function resolveNonPivotFields(NovaRequest $request)
    {
        return $this->availableFields($request)
            ->resolve($this->resource)
            ->authorized($request);
    }

    /**
     * Resolve the field for the given attribute.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $attribute
     * @return \Laravel\Nova\Fields\Field
     */
    public function resolveFieldForAttribute(NovaRequest $request, $attribute)
    {
        return $this->resolveFields($request)->findFieldByAttribute($attribute);
    }

    /**
     * Resolve the inverse field for the given relationship attribute.
     *
     * This is primarily used for Relatable rule to check if has-one / morph-one relationships are "full".
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $attribute
     * @param  string|null  $morphType
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function resolveInverseFieldsForAttribute(NovaRequest $request, $attribute, $morphType = null)
    {
        $field = $this->availableFields($request)
            ->findFieldByAttribute($attribute);

        if (! (! is_null($field) && $field->authorize($request) && isset($field->resourceClass))) {
            return new FieldCollection;
        }

        /** @var class-string<\Laravel\Nova\Resource> $relatedResource */
        $relatedResource = $field instanceof MorphTo
            ? Nova::resourceForKey($morphType ?? $request->{$attribute.'_type'})
            : ($field->resourceClass ?? null);

        $relatedResource = new $relatedResource($relatedResource::newModel());

        return $relatedResource->availableFields($request)->reject(function ($f) use ($field) {
            return isset($f->attribute) &&
                isset($field->inverse) &&
                $f->attribute !== $field->inverse;
        })->filter(function ($field) use ($request) {
            return isset($field->resourceClass) &&
                $field->resourceClass == $request->resource();
        });
    }

    /**
     * Resolve the resource's avatar field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Contracts\Cover|null
     */
    public function resolveAvatarField(NovaRequest $request)
    {
        return tap(
            $this->availableFields($request)
                ->whereInstanceOf(Cover::class)
                ->authorized($request)
                ->first(),
            function ($field) {
                if ($field instanceof Resolvable) {
                    $field->resolve($this->resource);
                }
            }
        );
    }

    /**
     * Resolve the resource's avatar URL, if applicable.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string|null
     */
    public function resolveAvatarUrl(NovaRequest $request)
    {
        $field = $this->resolveAvatarField($request);

        if ($field) {
            return $field->resolveThumbnailUrl();
        }
    }

    /**
     * Determine whether the resource's avatar should be rounded, if applicable.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function resolveIfAvatarShouldBeRounded(NovaRequest $request)
    {
        $field = $this->resolveAvatarField($request);

        if ($field) {
            return $field->isRounded();
        }

        return false;
    }

    /**
     * Get the panels that are available for the given create request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>|null  $fields
     * @return array<int, \Laravel\Nova\Panel>
     */
    public function availablePanelsForCreate($request, FieldCollection $fields = null)
    {
        $method = $this->fieldsMethod($request);

        $fields = $fields ?? $this->removeNonCreationFields(
            $request,
            FieldCollection::make(value(function () use ($request, $method) {
                return array_values($this->{$method}($request));
            }))
        );

        return $this->resolvePanelsFromFields(
            $request,
            $fields,
            Panel::defaultNameForCreate($request->newResource())
        )->all();
    }

    /**
     * Get the panels that are available for the given update request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>|null  $fields
     * @return array<int, \Laravel\Nova\Panel>
     */
    public function availablePanelsForUpdate(NovaRequest $request, Resource $resource = null, FieldCollection $fields = null)
    {
        $method = $this->fieldsMethod($request);

        $fields = $fields ?? $this->removeNonUpdateFields(
            $request,
            FieldCollection::make(value(function () use ($request, $method) {
                return array_values($this->{$method}($request));
            }))
        );

        return $this->resolvePanelsFromFields(
            $request,
            $fields,
            Panel::defaultNameForUpdate($resource ?? $request->newResource())
        )->all();
    }

    /**
     * Get the panels that are available for the given detail request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @return array<int, \Laravel\Nova\Panel>
     */
    public function availablePanelsForDetail(NovaRequest $request, Resource $resource, FieldCollection $fields)
    {
        return $this->resolvePanelsFromFields(
            $request,
            $fields,
            $request->viaRelationship() && $request->isResourceDetailRequest()
                ? Panel::defaultNameForViaRelationship($resource, $request)
                : Panel::defaultNameForDetail($resource)
        )->all();
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function availableFields(NovaRequest $request)
    {
        $method = $this->fieldsMethod($request);

        return FieldCollection::make(array_values($this->filter($this->{$method}($request))));
    }

    /**
     * Get the fields that are available on "index" or "detail" for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function availableFieldsOnIndexOrDetail(NovaRequest $request)
    {
        return $this->buildAvailableFields($request, ['fieldsForIndex', 'fieldsForDetail']);
    }

    /**
     * Get the fields that are available for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array  $methods
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function buildAvailableFields(NovaRequest $request, array $methods)
    {
        $fields = collect([
            method_exists($this, 'fields') ? $this->fields($request) : [],
        ]);

        collect($methods)
            ->filter(function ($method) {
                return $method != 'fields' && method_exists($this, $method);
            })->each(function ($method) use ($request, $fields) {
                $fields->push([$this->{$method}($request)]);
            });

        return FieldCollection::make(array_values($this->filter($fields->flatten()->all())));
    }

    /**
     * Compute the method to use to get the available fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return string
     */
    protected function fieldsMethod(NovaRequest $request)
    {
        if ($request->isInlineCreateRequest() && method_exists($this, 'fieldsForInlineCreate')) {
            return 'fieldsForInlineCreate';
        }

        if ($request->isResourceIndexRequest() && method_exists($this, 'fieldsForIndex')) {
            return 'fieldsForIndex';
        }

        if ($request->isResourceDetailRequest() && method_exists($this, 'fieldsForDetail')) {
            return 'fieldsForDetail';
        }

        if ($request->isCreateOrAttachRequest() && method_exists($this, 'fieldsForCreate')) {
            return 'fieldsForCreate';
        }

        if ($request->isUpdateOrUpdateAttachedRequest() && method_exists($this, 'fieldsForUpdate')) {
            return 'fieldsForUpdate';
        }

        return 'fields';
    }

    /**
     * Merge the available pivot fields with the given fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array<int, \Laravel\Nova\Fields\Field>  $fields
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    protected function withPivotFields(NovaRequest $request, array $fields)
    {
        $pivotFields = $this->resolvePivotFields($request, $request->viaResource)->all();

        if ($index = $this->indexToInsertPivotFields($request, $fields)) {
            array_splice($fields, $index + 1, 0, $pivotFields);
        } else {
            $fields = array_merge($fields, $pivotFields);
        }

        return FieldCollection::make($fields);
    }

    /**
     * Resolve the pivot fields for the requested resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $relatedResource
     * @return \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>
     */
    public function resolvePivotFields(NovaRequest $request, $relatedResource)
    {
        $fields = $this->pivotFieldsFor($request, $relatedResource);

        return FieldCollection::make($this->filter($fields->each(function ($field) {
            if ($field instanceof Resolvable) {
                $field->resolve(
                    $this->{$field->pivotAccessor} ?? $field->pivotRelation->newPivot($field->pivotRelation->getDefaultPivotAttributes(), false)
                );
            }
        })->authorized($request)->all()))->values();
    }

    /**
     * Get the pivot fields for the resource and relation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $relatedResource
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    protected function pivotFieldsFor(NovaRequest $request, $relatedResource)
    {
        $fields = $this->availableFields($request)->filter(function ($field) use ($relatedResource) {
            return ($field instanceof BelongsToMany || $field instanceof MorphToMany) &&
                isset($field->resourceName) && $field->resourceName == $relatedResource;
        });

        /** @var \Laravel\Nova\Fields\BelongsToMany|\Laravel\Nova\Fields\MorphToMany|null $field */
        $field = $fields->count() === 1
            ? $fields->first(function ($field) {
                return $field;
            }) : $fields->first(function ($field) use ($request) {
                return $field->manyToManyRelationship === $request->viaRelationship;
            });

        if ($field && isset($field->fieldsCallback)) {
            $pivotRelation = $this->resource->{$field->manyToManyRelationship}();
            $field->pivotAccessor = $pivotAccessor = $pivotRelation->getPivotAccessor();

            return FieldCollection::make(array_values(
                $this->filter(call_user_func($field->fieldsCallback, $request, $this->resource))
            ))->each(function ($field) use ($pivotAccessor, $pivotRelation) {
                $field->pivot = true;
                $field->pivotAccessor = $pivotAccessor;
                $field->pivotRelation = $pivotRelation;
            });
        }

        return FieldCollection::make();
    }

    /**
     * Get the pivot fields for the resource and relation from related relationship.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $relatedResource
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    protected function relatedPivotFieldsFor(NovaRequest $request, $relatedResource)
    {
        $resource = Nova::resourceInstanceForKey($relatedResource);

        $fields = $resource->availableFields($request)->filter(function ($field) {
            return ($field instanceof BelongsToMany || $field instanceof MorphToMany) &&
                isset($field->resourceName) && $field->resourceName == $this->uriKey();
        });

        /** @var \Laravel\Nova\Fields\BelongsToMany|\Laravel\Nova\Fields\MorphToMany|null $field */
        $field = $fields->count() === 1
            ? $fields->first(function ($field) {
                return $field;
            }) : $fields->first(function ($field) use ($request) {
                return $field->manyToManyRelationship === $request->viaRelationship;
            });

        if ($field && isset($field->fieldsCallback)) {
            $pivotRelation = $resource->model()->{$field->manyToManyRelationship}();
            $field->pivotAccessor = $pivotAccessor = $pivotRelation->getPivotAccessor();

            return FieldCollection::make(array_values(
                $this->filter(call_user_func($field->fieldsCallback, $request, $this->resource))
            ))->each(function ($field) use ($pivotAccessor, $pivotRelation) {
                $field->pivot = true;
                $field->pivotAccessor = $pivotAccessor;
                $field->pivotRelation = $pivotRelation;
            });
        }

        return FieldCollection::make();
    }

    /**
     * Get the index where the pivot fields should be spliced into the field array.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  array<int, \Laravel\Nova\Fields\Field>  $fields
     * @return int|null
     */
    protected function indexToInsertPivotFields(NovaRequest $request, array $fields)
    {
        foreach ($fields as $index => $field) {
            if (
                isset($field->resourceName) &&
                $field->resourceName == $request->viaResource
            ) {
                return $index;
            }
        }
    }

    /**
     * Get the displayable pivot model name from a field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $field
     * @return string|null
     */
    public function pivotNameForField(NovaRequest $request, $field)
    {
        $field = $this->availableFields($request)->findFieldByAttribute($field);

        if (! ($field instanceof BelongsToMany || $field instanceof MorphToMany)) {
            return self::DEFAULT_PIVOT_NAME;
        }

        if (isset($field->pivotName)) {
            return $field->pivotName;
        }
    }

    /**
     * Resolve available panels from fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @param  string  $label
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Panel>
     */
    protected function resolvePanelsFromFields(NovaRequest $request, FieldCollection $fields, $label)
    {
        [$defaultFields, $fieldsWithPanels] = $fields->each(function ($field) {
            if ($field instanceof BehavesAsPanel) {
                $field->asPanel();
            }
        })->partition(function ($field) {
            return ! isset($field->panel) || blank($field->panel);
        });

        $panels = $fieldsWithPanels->groupBy(function ($field) {
            return $field->panel;
        })->transform(function ($fields, $name) {
            return Panel::mutate($name, $fields);
        })->toBase();

        return $this->panelsWithDefaultLabel(
            $panels,
            $defaultFields->values(),
            $label
        );
    }

    /**
     * Return the panels for this request with the default label.
     *
     * @param  \Illuminate\Support\Collection<int, \Laravel\Nova\Panel>  $panels
     * @param  \Laravel\Nova\Fields\FieldCollection<int, \Laravel\Nova\Fields\Field>  $fields
     * @param  string  $label
     * @return \Illuminate\Support\Collection<int, \Laravel\Nova\Panel>
     */
    protected function panelsWithDefaultLabel(Collection $panels, FieldCollection $fields, $label)
    {
        return $panels->values()
            ->when($panels->where('name', $label)->isEmpty(), function ($panels) use ($label, $fields) {
                return $fields->isNotEmpty()
                    ? $panels->prepend(Panel::make($label, $fields)->withMeta(['fields' => $fields]))
                    : $panels;
            })->tap(function ($panels) {
                tap($panels->first(), function ($panel) {
                    if (! is_null($panel)) {
                        $panel->withToolbar();
                    }
                });
            });
    }

    /**
     * Return the callback used for resolving fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Closure(\Laravel\Nova\Fields\FieldCollection):\Laravel\Nova\Fields\FieldCollection
     */
    protected function fieldResolverCallback(NovaRequest $request)
    {
        return function ($fields) use ($request) {
            $fields = $fields->values()->all();
            $pivotFields = $this->pivotFieldsFor($request, $request->viaResource)->all();

            if (! is_null($index = $this->indexToInsertPivotFields($request, $fields))) {
                array_splice($fields, $index + 1, 0, $pivotFields);
            } else {
                $fields = array_merge($fields, $pivotFields);
            }

            return FieldCollection::make($fields);
        };
    }

    /**
     * Return the callback used for resolving fields with pivot from related relationship.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Closure(\Laravel\Nova\Fields\FieldCollection):\Laravel\Nova\Fields\FieldCollection
     */
    protected function relatedFieldResolverCallback(NovaRequest $request)
    {
        return function ($fields) use ($request) {
            $fields = $fields->values()->all();
            $pivotFields = $this->relatedPivotFieldsFor($request, $request->viaResource)->all();

            if (! is_null($index = $this->indexToInsertPivotFields($request, $fields))) {
                array_splice($fields, $index + 1, 0, $pivotFields);
            } else {
                $fields = array_merge($fields, $pivotFields);
            }

            return FieldCollection::make($fields);
        };
    }
}
