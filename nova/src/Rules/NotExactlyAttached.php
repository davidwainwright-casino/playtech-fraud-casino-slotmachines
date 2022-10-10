<?php

namespace Laravel\Nova\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Laravel\Nova\ResourceToolElement;

class NotExactlyAttached implements Rule
{
    /**
     * The request instance.
     *
     * @var \Laravel\Nova\Http\Requests\NovaRequest
     */
    public $request;

    /**
     * The model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /**
     * Create a new rule instance.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|null  $model
     * @return void
     */
    public function __construct(NovaRequest $request, $model)
    {
        $this->model = $model;
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        /** @var \Illuminate\Database\Eloquent\Relations\MorphToMany|\Illuminate\Database\Eloquent\Relations\BelongsToMany $relation */
        $relation = $this->model->{$this->request->viaRelationship}();

        $pivot = $relation->newPivot();
        $query = $relation->withoutGlobalScopes()
                        ->where($relation->getQualifiedRelatedPivotKeyName(), '=', $this->request->input($this->request->relatedResource));

        $resource = Nova::newResourceFromModel($this->model);

        $resource->resolvePivotFields($this->request, $this->request->relatedResource)
            ->reject(function ($field) {
                return $field instanceof ResourceToolElement || $field->computed();
            })
            ->each(function ($field) use ($pivot) {
                $field->fill($this->request, $pivot, $field->attribute);
            });

        $attributes = $pivot->toArray();

        foreach ($query->cursor() as $result) {
            $pivots = Arr::only($result->pivot->toArray(), array_keys($attributes));

            if (array_diff_assoc(Arr::flatten($pivots), Arr::flatten($attributes)) === []) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('nova::validation.attached');
    }
}
