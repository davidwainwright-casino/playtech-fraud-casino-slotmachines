<?php

namespace Laravel\Nova\Fields;

use Illuminate\Http\Request;
use Laravel\Nova\Contracts\BehavesAsPanel;
use Laravel\Nova\Contracts\RelatableField;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Util;

/**
 * @method static static make(mixed $name, string|null $attribute = null, string|null $resource = null)
 */
class HasOne extends Field implements RelatableField, BehavesAsPanel
{
    use FormatsRelatableDisplayValues;

    /**
     * Indicates if the related resource can be viewed.
     *
     * @var bool
     */
    public $viewable = true;

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'has-one-field';

    /**
     * The class name of the related resource.
     *
     * @var class-string<\Laravel\Nova\Resource>
     */
    public $resourceClass;

    /**
     * The URI key of the related resource.
     *
     * @var string
     */
    public $resourceName;

    /**
     * The displayable singular label of the relation.
     *
     * @var string
     */
    public $singularLabel;

    /**
     * The resolved HasOne Resource.
     *
     * @var \Laravel\Nova\Resource|null
     */
    public $hasOneResource;

    /**
     * The name of the Eloquent "has one" relationship.
     *
     * @var string
     */
    public $hasOneRelationship;

    /**
     * The key of the related Eloquent model.
     *
     * @var string|int|null
     */
    public $hasOneId;

    /**
     * The callback use to determine if the HasOne field has already been filled.
     *
     * @var \Closure(\Laravel\Nova\Http\Requests\NovaRequest):bool
     */
    public $filledCallback;

    /**
     * Determine one-of-many relationship.
     *
     * @var bool
     */
    protected $ofManyRelationship = false;

    /**
     * The cached field is required status.
     *
     * @var bool|null
     */
    protected $isRequired = null;

    /**
     * Create a new field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  class-string<\Laravel\Nova\Resource>|null  $resource
     * @return void
     */
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute);

        $resource = $resource ?? ResourceRelationshipGuesser::guessResource($name);

        $this->resourceClass = $resource;
        $this->resourceName = $resource::uriKey();
        $this->hasOneRelationship = $this->attribute = $attribute ?? ResourceRelationshipGuesser::guessRelation($name);
        $this->singularLabel = $resource::singularLabel();

        $this->alreadyFilledWhen(function ($request) {
            $parentResource = Nova::resourceForKey($request->viaResource);

            if ($this->ofManyRelationship === false && $request->viaRelationship === $this->attribute && $request->viaResourceId) {
                $parent = $parentResource::newModel()
                            ->with($this->attribute)
                            ->find($request->viaResourceId);

                return optional($parent->{$this->attribute})->exists === true;
            }

            return false;
        })->showOnCreating(function ($request) {
            return ! in_array($request->relationshipType, ['hasOne', 'morphOne']);
        })->showOnUpdating(function ($request) {
            return ! in_array($request->relationshipType, ['hasOne', 'morphOne']);
        })->nullable();
    }

    /**
     * Make one-of-many relationship field.
     *
     * @param  string  $name
     * @param  string|null  $attribute
     * @param  class-string<\Laravel\Nova\Resource>|null  $resource
     * @return static
     */
    public static function ofMany($name, $attribute = null, $resource = null)
    {
        return tap(new static($name, $attribute, $resource), function ($field) {
            $field->ofManyRelationship = true;
            $field->readonly();
            $field->onlyOnDetail();
        });
    }

    /**
     * Get the relationship name.
     *
     * @return string
     */
    public function relationshipName()
    {
        return $this->hasOneRelationship;
    }

    /**
     * Get the relationship type.
     *
     * @return string
     */
    public function relationshipType()
    {
        return 'hasOne';
    }

    /**
     * Determine if the field should be displayed for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorize(Request $request)
    {
        return call_user_func(
            [$this->resourceClass, 'authorizedToViewAny'], $request
        ) && parent::authorize($request);
    }

    /**
     * Resolve the field's value.
     *
     * @param  mixed  $resource
     * @param  string|null  $attribute
     * @return void
     */
    public function resolve($resource, $attribute = null)
    {
        $value = null;

        if ($resource->relationLoaded($this->attribute)) {
            $value = $resource->getRelation($this->attribute);
        }

        if (! $value) {
            $value = $resource->{$this->attribute}()->withoutGlobalScopes()->getResults();
        }

        if ($value) {
            $this->alreadyFilledWhen(function () use ($value) {
                return optional($value)->exists;
            });

            $this->hasOneResource = new $this->resourceClass($value);

            $this->hasOneId = optional(ID::forResource($this->hasOneResource))->value ?? $value->getKey();

            $this->value = $this->hasOneId;
        }
    }

    /**
     * Set the displayable singular label of the resource.
     *
     * @param  string  $singularLabel
     * @return $this
     */
    public function singularLabel($singularLabel)
    {
        $this->singularLabel = $singularLabel;

        return $this;
    }

    /**
     * Make current field behaves as panel.
     *
     * @return \Laravel\Nova\Panel
     */
    public function asPanel()
    {
        return Panel::make($this->name, [$this])
                    ->withMeta([
                        'prefixComponent' => true,
                    ])->withComponent('relationship-panel');
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return with(app(NovaRequest::class), function ($request) {
            if (! is_null($this->requiredCallback)) {
                $this->nullable = ! with($this->requiredCallback, function ($callback) use ($request) {
                    return $callback === true || (is_callable($callback) && call_user_func($callback, $request));
                });
            }

            return array_merge([
                'resourceName' => $this->resourceName,
                'hasOneRelationship' => $this->hasOneRelationship,
                'relationshipType' => $this->relationshipType(),
                'relationId' => $this->hasOneId,
                'hasOneId' => $this->hasOneId,
                'relatable' => true,
                'singularLabel' => $this->singularLabel,
                'alreadyFilled' => $this->alreadyFilled($request),
                'authorizedToView' => optional($this->hasOneResource)->authorizedToView($request) ?? true,
                'authorizedToCreate' => $this->ofManyRelationship === true ? false : $this->resourceClass::authorizedToCreate($request),
                'createButtonLabel' => $this->resourceClass::createButtonLabel(),
                'from' => array_filter([
                    'viaResource' => $request->resource,
                    'viaResourceId' => $request->resourceId,
                    'viaRelationship' => $request->viaRelationship,
                ]),
            ], parent::jsonSerialize());
        });
    }

    /**
     * Determine if the field is required.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isRequired(NovaRequest $request)
    {
        if (is_null($this->isRequired)) {
            $this->isRequired = parent::isRequired($request);
        }

        $this->nullable = ! $this->isRequired;

        return $this->isRequired;
    }

    /**
     * Set the Closure used to determine if the HasOne field has already been filled.
     *
     * @param  \Closure(\Laravel\Nova\Http\Requests\NovaRequest):bool  $callback
     * @return $this
     */
    public function alreadyFilledWhen($callback)
    {
        $this->filledCallback = $callback;

        return $this;
    }

    /**
     * Determine if the HasOne field has alreaady been filled.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function alreadyFilled(NovaRequest $request)
    {
        return call_user_func($this->filledCallback, $request) ?? false;
    }

    /**
     * Check showing on index.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnIndex(NovaRequest $request, $resource): bool
    {
        return false;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  object  $model
     * @param  string  $attribute
     * @param  string|null  $requestAttribute
     * @return (\Closure():void)|null
     */
    public function fillInto(NovaRequest $request, $model, $attribute, $requestAttribute = null)
    {
        $resourceClass = $this->resourceClass;
        $relation = $model->loadMissing($this->hasOneRelationship)->getRelation($this->hasOneRelationship) ?? $resourceClass::newModel();

        $editMode = $relation->exists === false ? 'create' : 'update';

        $filled = collect($request->{$attribute} ?? [])->filter()->isNotEmpty();

        if (
            $this->ofManyRelationship === true
            || ($this->nullable && ! $filled && $editMode === 'create')
        ) {
            return null;
        }

        $resourceClass = $this->resourceClass;
        $resource = new $resourceClass($relation);

        $callbacks = $resource->availableFields($request)
            ->map(function ($field) use ($request, $relation, $attribute) {
                return $field->fillInto($request, $relation, $field->attribute, "{$attribute}.{$field->attribute}");
            });

        if ($editMode === 'create') {
            $callbacks->prepend(function () use ($request, $relation, $model) {
                $model->{$this->hasOneRelationship}()->save($relation);

                Nova::usingActionEvent(function ($actionEvent) use ($request, $relation) {
                    $actionEvent->forResourceCreate(Nova::user($request), $relation)->save();
                });
            });
        } else {
            Nova::usingActionEvent(function ($actionEvent) use ($request, $relation) {
                $actionEvent->forResourceUpdate(Nova::user($request), $relation)->save();
            });

            $relation->save();
        }

        $model->setRelation($this->hasOneRelationship, $relation);

        return function () use ($callbacks) {
            $callbacks->filter(function ($callback) {
                return is_callable($callback);
            })->each->__invoke();
        };
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<string, array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>>
     */
    public function getCreationRules(NovaRequest $request)
    {
        return $this->getAvailableValidationRules($request);
    }

    /**
     * Get the update rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<string, array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>>
     */
    public function getUpdateRules(NovaRequest $request)
    {
        return $this->getAvailableValidationRules($request);
    }

    /**
     * Get the available rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<string, array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>>
     */
    protected function getAvailableValidationRules(NovaRequest $request)
    {
        $model = $request->findModel();
        $resourceClass = $this->resourceClass;

        $relation = method_exists($model, $this->hasOneRelationship)
            ? $model->loadMissing($this->hasOneRelationship)->getRelation($this->hasOneRelationship) ?? $resourceClass::newModel()
            : null;

        if (is_null($relation)) {
            return [];
        }

        $resource = new $resourceClass($relation);

        return $relation->exists === false
                    ? $this->getResourceCreationRules($request, $resource)
                    : $this->getResourceUpdateRules($request, $resource);
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return array<string, array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>>
     */
    public function getResourceCreationRules(NovaRequest $request, $resource)
    {
        $replacements = Util::dependentRules($this->attribute);

        return $resource->creationFields($request)
            ->reject(function ($field) use ($request) {
                return $field instanceof BelongsTo && $field->resourceClass == Nova::resourceForKey($request->resource);
            })
            ->applyDependsOn($request)
            ->mapWithKeys(function ($field) use ($request) {
                return $field->getCreationRules($request);
            })
            ->mapWithKeys(function ($field, $attribute) use ($replacements) {
                if ($this->nullable === true) {
                    array_push($field, 'sometimes');
                }

                return ["{$this->attribute}.{$attribute}" => collect($field)->transform(function ($rule) use ($replacements) {
                    if (empty($replacements)) {
                        return $rule;
                    }

                    return is_string($rule)
                            ? str_replace(array_keys($replacements), array_values($replacements), $rule)
                            : $rule;
                })->all()];
            })
            ->prepend(['array', $this->nullable === true ? 'nullable' : 'required'], $this->attribute)
            ->all();
    }

    /**
     * Get the update rules for this resource fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return array<string, array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>>
     */
    public function getResourceUpdateRules(NovaRequest $request, $resource)
    {
        $replacements = collect([
            '{{resourceId}}' => str_replace(['\'', '"', ',', '\\'], '', $resource->model()->getKey() ?? ''),
        ])->merge(
            Util::dependentRules($this->attribute),
        )->filter()->all();

        return $resource->updateFields($request)
            ->reject($this->rejectRecursiveRelatedResourceFields($request))
            ->applyDependsOn($request)
            ->mapWithKeys(function ($field) use ($request) {
                return $field->getUpdateRules($request);
            })
            ->mapWithKeys(function ($field, $attribute) use ($replacements) {
                if ($this->nullable === true) {
                    array_push($field, 'sometimes');
                }

                return ["{$this->attribute}.{$attribute}" => collect($field)->transform(function ($rule) use ($replacements) {
                    if (empty($replacements)) {
                        return $rule;
                    }

                    return is_string($rule)
                            ? str_replace(array_keys($replacements), array_values($replacements), $rule)
                            : $rule;
                })->all()];
            })
            ->prepend(['array', $this->nullable === true ? 'nullable' : 'required'], $this->attribute)
            ->all();
    }

    /**
     * Get the validation attribute names for the field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<string, string>
     */
    public function getValidationAttributeNames(NovaRequest $request)
    {
        $resourceClass = $this->resourceClass;
        $resource = new $resourceClass($resourceClass::newModel());

        return $resource->updateFields($request)
            ->reject($this->rejectRecursiveRelatedResourceFields($request))
            ->reject(function ($field) {
                return empty($field->name);
            })
            ->mapWithKeys(function ($field) {
                return ["{$this->attribute}.{$field->attribute}" => $field->name];
            })->all();
    }

    /**
     * Determine if the relationship is a of-many relationship.
     *
     * @return bool
     */
    public function ofManyRelationship()
    {
        return $this->ofManyRelationship;
    }

    /**
     * Check for showing when creating.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return bool
     */
    public function isShownOnCreation(NovaRequest $request): bool
    {
        return call_user_func($this->rejectRecursiveRelatedResourceFields($request), $this) === false
            && parent::isShownOnCreation($request);
    }

    /**
     * Check for showing when updating.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  mixed  $resource
     * @return bool
     */
    public function isShownOnUpdate(NovaRequest $request, $resource): bool
    {
        return call_user_func($this->rejectRecursiveRelatedResourceFields($request), $this) === false
            && parent::isShownOnUpdate($request, $resource);
    }

    /**
     * Reject recursive related resource fields.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Closure
     */
    protected function rejectRecursiveRelatedResourceFields(NovaRequest $request)
    {
        return function ($field) use ($request) {
            if (! $field instanceof RelatableField) {
                return false;
            }

            $relatedResource = $field->resourceName == $request->resource;

            return ($this->relationshipType() === 'hasOne' && $field instanceof BelongsTo && $relatedResource) ||
                ($this->relationshipType() === 'morphOne' && $field instanceof MorphTo && $relatedResource);
        };
    }
}
