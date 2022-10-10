<?php

namespace Laravel\Nova\Fields;

use Illuminate\Support\Arr;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Rules\NotAttached;
use Laravel\Nova\Rules\NotExactlyAttached;

trait ManyToManyCreationRules
{
    /**
     * The callback that should be used to set creation rules callback for the pivot actions.
     *
     * @var (callable(\Laravel\Nova\Http\Requests\NovaRequest):array)|null
     */
    public $creationRulesCallback;

    /**
     * Determine if field allow duplicate relations.
     *
     * @var bool
     */
    public $allowDuplicateRelations = false;

    /**
     * Set creation rules callback for this relation.
     *
     * @param  (callable(\Laravel\Nova\Http\Requests\NovaRequest):array)|null  $callback
     * @return $this
     */
    public function creationRules($callback = null)
    {
        $this->creationRulesCallback = $callback;

        return $this;
    }

    /**
     * Set allow same relation rules.
     *
     * @return $this
     */
    public function allowDuplicateRelations()
    {
        $this->allowDuplicateRelations = true;

        return $this->creationRules(function ($request) {
            return [
                new NotExactlyAttached($request, $request->findModelOrFail()),
            ];
        });
    }

    /**
     * Set disallow same relation rules.
     *
     * @return $this
     */
    public function noDuplicateRelations()
    {
        $this->allowDuplicateRelations = false;

        return $this->creationRules(function ($request) {
            return [
                new NotAttached($request, $request->findModelOrFail()),
            ];
        });
    }

    /**
     * Get the creation rules for this field.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array<int, string|\Illuminate\Validation\Rule|\Illuminate\Contracts\Validation\Rule|callable>
     */
    public function getManyToManyCreationRules(NovaRequest $request)
    {
        return transform($this->creationRulesCallback, function ($callback) use ($request) {
            return Arr::wrap(call_user_func($callback, $request));
        }, []);
    }
}
