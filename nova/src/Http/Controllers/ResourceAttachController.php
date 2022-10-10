<?php

namespace Laravel\Nova\Http\Controllers;

use DateTime;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Throwable;

class ResourceAttachController extends Controller
{
    use HandlesCustomRelationKeys;

    /**
     * The action event for the action.
     *
     * @var \Laravel\Nova\Actions\ActionEvent|null
     */
    protected $actionEvent;

    /**
     * Attach a related resource to the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(NovaRequest $request)
    {
        $resource = $request->resource();

        $model = $request->findModelOrFail();

        tap(new $resource($model), function ($resource) use ($request) {
            abort_unless($resource->hasRelatableField($request, $request->viaRelationship), 404);
        });

        $this->validate($request, $model, $resource);

        try {
            DB::connection($model->getConnectionName())->transaction(function () use ($request, $resource, $model) {
                [$pivot, $callbacks] = $resource::fillPivot(
                    $request,
                    $model,
                    $this->initializePivot(
                        $request,
                        $model->{$request->viaRelationship}()
                    )
                );

                DB::transaction(function () use ($request, $model, $pivot) {
                    Nova::usingActionEvent(function ($actionEvent) use ($request, $model, $pivot) {
                        $this->actionEvent = $actionEvent->forAttachedResource($request, $model, $pivot);
                        $this->actionEvent->save();
                    });
                });

                $pivot->save();

                collect($callbacks)->each->__invoke();
            });

            return response()->noContent(200);
        } catch (Throwable $e) {
            optional($this->actionEvent)->delete();
            throw $e;
        }
    }

    /**
     * Validate the attachment request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return void
     */
    protected function validate(NovaRequest $request, $model, $resourceClass)
    {
        $attribute = $resourceClass::validationAttachableAttributeFor($request, $request->relatedResource);

        tap($this->creationRules($request, $resourceClass), function ($rules) use ($resourceClass, $request, $attribute) {
            Validator::make($request->all(), $rules, [], $this->customRulesKeys($request, $attribute))->validate();

            $resourceClass::validateForAttachment($request);
        });
    }

    /**
     * Return the validation rules used for the request. Correctly aasign the rules used
     * to the main attribute if the user has defined a custom relation key.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  class-string<\Laravel\Nova\Resource>  $resourceClass
     * @return mixed
     */
    protected function creationRules(NovaRequest $request, $resourceClass)
    {
        $rules = $resourceClass::creationRulesFor($request, $this->getRuleKey($request));

        if ($this->usingCustomRelationKey($request)) {
            $rules[$request->relatedResource] = $rules[$request->viaRelationship];
            unset($rules[$request->viaRelationship]);
        }

        return $rules;
    }

    /**
     * Initialize a fresh pivot model for the relationship.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Relations\BelongsToMany  $relationship
     * @return \Illuminate\Database\Eloquent\Relations\Pivot
     *
     * @throws \Exception
     */
    protected function initializePivot(NovaRequest $request, $relationship)
    {
        $parentKey = $request->resourceId;
        $relatedKey = $request->input($request->relatedResource);

        $parentKeyName = $relationship->getParentKeyName();
        $relatedKeyName = $relationship->getRelatedKeyName();

        if ($parentKeyName !== $request->model()->getKeyName()) {
            $parentKey = $request->findModelOrFail()->{$parentKeyName};
        }

        if ($relatedKeyName !== ($request->newRelatedResource()::newModel())->getKeyName()) {
            $relatedKey = $request->findRelatedModelOrFail()->{$relatedKeyName};
        }

        ($pivot = $relationship->newPivot($relationship->getDefaultPivotAttributes(), false))->forceFill([
            $relationship->getForeignPivotKeyName() => $parentKey,
            $relationship->getRelatedPivotKeyName() => $relatedKey,
        ]);

        if ($relationship->withTimestamps) {
            $pivot->forceFill([
                $relationship->createdAt() => new DateTime,
                $relationship->updatedAt() => new DateTime,
            ]);
        }

        return $pivot;
    }
}
