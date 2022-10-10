<?php

namespace Laravel\Nova\Fields;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Util;

/**
 * @method static static make(mixed $name = null, string|null $attribute = null, callable|null $resolveCallback = null)
 */
class ID extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'id-field';

    /**
     * The field's resolved pivot value.
     *
     * @var mixed
     */
    public $pivotValue = null;

    /**
     * Create a new field.
     *
     * @param  string|null  $name
     * @param  string|null  $attribute
     * @param  (callable(mixed, mixed, ?string):mixed)|null  $resolveCallback
     * @return void
     */
    public function __construct($name = null, $attribute = null, $resolveCallback = null)
    {
        parent::__construct($name ?? 'ID', $attribute, $resolveCallback);
    }

    /**
     * Create a new, resolved ID field for the given resource.
     *
     * @param  \Laravel\Nova\Resource  $resource
     * @return static
     */
    public static function forResource($resource)
    {
        $model = $resource->model();

        $field = transform(
            $resource->availableFieldsOnIndexOrDetail(app(NovaRequest::class))
                    ->whereInstanceOf(self::class)
                    ->first(),
            function ($field) use ($model) {
                return tap($field)->resolve($model);
            },
            function () use ($model) {
                return ! is_null($model) && $model->exists ? static::forModel($model) : null;
            }
        );

        return empty($field->value) && $field->nullable !== true ? null : $field;
    }

    /**
     * Create a new, resolved ID field for the given model.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return static
     */
    public static function forModel($model)
    {
        return tap(static::make('ID', $model->getKeyName()), function ($field) use ($model) {
            $value = $model->getKey();

            if (is_int($value) && $value >= 9007199254740991) {
                $field->asBigInt();
            }

            $field->resolve($model);
        });
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        if (! is_null($resource)) {
            $pivotValue = isset($resource->pivot) ? optional($resource->pivot)->getKey() : null;

            if (is_int($pivotValue) || is_string($pivotValue)) {
                $this->pivotValue = $pivotValue;
            }
        }

        return Util::safeInt(
            parent::resolveAttribute($resource, $attribute)
        );
    }

    /**
     * Resolve a BIGINT ID field as a string for compatibility with JavaScript.
     *
     * @return $this
     */
    public function asBigInt()
    {
        $this->resolveCallback = function ($id) {
            return (string) $id;
        };

        return $this;
    }

    /**
     * Hide the ID field from the Nova interface but keep it available for operations.
     *
     * @return $this
     */
    public function hide()
    {
        $this->showOnIndex = false;
        $this->showOnDetail = false;
        $this->showOnCreation = false;
        $this->showOnUpdate = false;

        return $this;
    }

    /**
     * Prepare the field for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), array_filter([
            'pivotValue' => $this->pivotValue ?? null,
        ]));
    }
}
