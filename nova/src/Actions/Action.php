<?php

namespace Laravel\Nova\Actions;

use Closure;
use Illuminate\Bus\PendingBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Laravel\Nova\AuthorizedToSee;
use Laravel\Nova\Exceptions\MissingActionHandlerException;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\Metable;
use Laravel\Nova\Nova;
use Laravel\Nova\ProxiesCanSeeToGate;
use ReflectionClass;

class Action implements JsonSerializable
{
    use AuthorizedToSee,
        Macroable,
        Makeable,
        Metable,
        ProxiesCanSeeToGate;

    /**
     * The displayable name of the action.
     *
     * @var string
     */
    public $name;

    /**
     * The action's component.
     *
     * @var string
     */
    public $component = 'confirm-action-modal';

    /**
     * Indicates if need to skip log action events for models.
     *
     * @var bool
     */
    public $withoutActionEvents = false;

    /**
     * Determine where the action redirection should be without confirmation.
     *
     * @var bool
     */
    public $withoutConfirmation = false;

    /**
     * Indicates if this action is only available on the resource index view.
     *
     * @var bool
     */
    public $onlyOnIndex = false;

    /**
     * Indicates if this action is only available on the resource detail view.
     *
     * @var bool
     */
    public $onlyOnDetail = false;

    /**
     * Indicates if this action is available on the resource index view.
     *
     * @var bool
     */
    public $showOnIndex = true;

    /**
     * Indicates if this action is available on the resource detail view.
     *
     * @var bool
     */
    public $showOnDetail = true;

    /**
     * Indicates if this action is available on the resource's table row.
     *
     * @var bool
     */
    public $showInline = false;

    /**
     * The current batch ID being handled by the action.
     *
     * @var string|null
     */
    public $actionBatchId;

    /**
     * The callback used to authorize running the action.
     *
     * @var (\Closure(\Laravel\Nova\Http\Requests\NovaRequest, mixed):bool)|null
     */
    public $runCallback;

    /**
     * The callback that should be invoked when the action has completed.
     *
     * @var (\Closure(\Illuminate\Support\Collection):mixed)|null
     */
    public $thenCallback;

    /**
     * The number of models that should be included in each chunk.
     *
     * @var int
     */
    public static $chunkCount = 200;

    /**
     * The text to be used for the action's confirm button.
     *
     * @var string
     */
    public $confirmButtonText = 'Run Action';

    /**
     * The text to be used for the action's cancel button.
     *
     * @var string
     */
    public $cancelButtonText = 'Cancel';

    /**
     * The text to be used for the action's confirmation text.
     *
     * @var string
     */
    public $confirmText = 'Are you sure you want to run this action?';

    /**
     * Indicates if the action can be run without any models.
     *
     * @var bool
     */
    public $standalone = false;

    /**
     * The XHR response type on executing the action.
     *
     * @var string
     */
    public $responseType = 'json';

    /**
     * Determine if the action is executable for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return bool
     */
    public function authorizedToRun(Request $request, $model)
    {
        return $this->runCallback ? call_user_func($this->runCallback, $request, $model) : true;
    }

    /**
     * Return a message response from the action.
     *
     * @param  string  $message
     * @return array<string, string>
     */
    public static function message($message)
    {
        return ['message' => $message];
    }

    /**
     * Return a dangerous message response from the action.
     *
     * @param  string  $message
     * @return array<string, string>
     */
    public static function danger($message)
    {
        return ['danger' => $message];
    }

    /**
     * Return a delete response from the action.
     *
     * @return array<string, bool>
     */
    public static function deleted()
    {
        return ['deleted' => true];
    }

    /**
     * Return a redirect response from the action.
     *
     * @param  string  $url
     * @return array<string, string>
     */
    public static function redirect($url)
    {
        return ['redirect' => $url];
    }

    /**
     * Return a Inertia visit from the action.
     *
     * @deprecated
     *
     * @param  string  $path
     * @param  array<string, mixed>  $options
     * @return array<string, array<string, mixed>>
     */
    public static function push($path, $options = [])
    {
        return static::visit($path, $options);
    }

    /**
     * Return a Inertia visit from the action.
     *
     * @param  string  $path
     * @param  array<string, mixed>  $options
     * @return array<string, array<string, mixed>>
     */
    public static function visit($path, $options = [])
    {
        return [
            'visit' => [
                'path' => '/'.ltrim($path, '/'),
                'options' => $options,
            ],
        ];
    }

    /**
     * Return an open new tab response from the action.
     *
     * @param  string  $url
     * @return array<string, string>
     */
    public static function openInNewTab($url)
    {
        return ['openInNewTab' => $url];
    }

    /**
     * Return a download response from the action.
     *
     * @param  string  $url
     * @param  string  $name
     * @return array<string, string>
     */
    public static function download($url, $name)
    {
        return ['download' => $url, 'name' => $name];
    }

    /**
     * Return an action modal response from the action.
     *
     * @param  string  $modal
     * @param  array<string, mixed>  $data
     * @return array<string, string|mixed>
     */
    public static function modal($modal, $data)
    {
        return array_merge(['modal' => $modal], $data);
    }

    /**
     * Execute the action for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return mixed
     *
     * @throws \Laravel\Nova\Exceptions\MissingActionHandlerException|\Throwable
     */
    public function handleRequest(ActionRequest $request)
    {
        $fields = $request->resolveFields();

        $dispatcher = new DispatchAction($request, $this, $fields);

        if (method_exists($this, 'dispatchRequestUsing')) {
            $dispatcher->handleUsing($request, function ($request, $response, $fields) {
                return $this->dispatchRequestUsing($request, $response, $fields);
            });
        } else {
            $method = ActionMethod::determine($this, $request->targetModel());

            if (! method_exists($this, $method)) {
                throw MissingActionHandlerException::make($this, $method);
            }

            $this->standalone
                ? $dispatcher->handleStandalone($method)
                : $dispatcher->handleRequest($request, $method, static::$chunkCount);
        }

        $response = $dispatcher->dispatch();

        if (! $response->wasExecuted) {
            return static::danger(__('Sorry! You are not authorized to perform this action.'));
        }

        if ($this->thenCallback) {
            return call_user_func($this->thenCallback, collect($response->results)->flatten());
        }

        return $this->handleResult($fields, $response->results);
    }

    /**
     * Handle chunk results.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  array<int, mixed>  $results
     * @return mixed
     */
    public function handleResult(ActionFields $fields, $results)
    {
        return count($results) ? end($results) : null;
    }

    /**
     * Handle any post-validation processing.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     */
    protected function afterValidation(NovaRequest $request, $validator)
    {
        //
    }

    /**
     * Mark the action event record for the model as finished.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return int
     */
    protected function markAsFinished($model)
    {
        return $this->actionBatchId ? Nova::usingActionEvent(function ($actionEvent) use ($model) {
            $actionEvent->markAsFinished($this->actionBatchId, $model);
        }) : 0;
    }

    /**
     * Mark the action event record for the model as failed.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Throwable|string  $e
     * @return int
     */
    protected function markAsFailed($model, $e = null)
    {
        return $this->actionBatchId ? Nova::usingActionEvent(function ($actionEvent) use ($model, $e) {
            $actionEvent->markAsFailed($this->actionBatchId, $model, $e);
        }) : 0;
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }

    /**
     * Validate the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateFields(ActionRequest $request)
    {
        $fields = collect($this->fields($request));

        return Validator::make(
            $request->all(),
            $fields->mapWithKeys(function ($field) use ($request) {
                return $field->getCreationRules($request);
            })->all(),
            [],
            $fields->reject(function ($field) {
                return empty($field->name);
            })->mapWithKeys(function ($field) {
                return [$field->attribute => $field->name];
            })->all()
        )->after(function ($validator) use ($request) {
            $this->afterValidation($request, $validator);
        })->validate();
    }

    /**
     * Indicate that this action is only available on the resource index view.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyOnIndex($value = true)
    {
        $this->onlyOnIndex = $value;
        $this->showOnIndex = $value;
        $this->showOnDetail = ! $value;
        $this->showInline = ! $value;

        return $this;
    }

    /**
     * Indicate that this action is available except on the resource index view.
     *
     * @return $this
     */
    public function exceptOnIndex()
    {
        $this->showOnDetail = true;
        $this->showInline = true;
        $this->showOnIndex = false;

        return $this;
    }

    /**
     * Indicate that this action is only available on the resource detail view.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyOnDetail($value = true)
    {
        $this->onlyOnDetail = $value;
        $this->showOnDetail = $value;
        $this->showOnIndex = ! $value;
        $this->showInline = ! $value;

        return $this;
    }

    /**
     * Indicate that this action is available except on the resource detail view.
     *
     * @return $this
     */
    public function exceptOnDetail()
    {
        $this->showOnIndex = true;
        $this->showOnDetail = false;
        $this->showInline = true;

        return $this;
    }

    /**
     * Indicate that this action is only available on the resource's table row.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyOnTableRow($value = true)
    {
        return $this->onlyInline($value);
    }

    /**
     * Indicate that this action is only available on the resource's table row.
     *
     * @param  bool  $value
     * @return $this
     */
    public function onlyInline($value = true)
    {
        $this->showInline = $value;
        $this->showOnIndex = ! $value;
        $this->showOnDetail = ! $value;

        return $this;
    }

    /**
     * Indicate that this action is available except on the resource's table row.
     *
     * @return $this
     */
    public function exceptOnTableRow()
    {
        return $this->exceptInline();
    }

    /**
     * Indicate that this action is available except on the resource's table row.
     *
     * @return $this
     */
    public function exceptInline()
    {
        $this->showInline = false;
        $this->showOnIndex = true;
        $this->showOnDetail = true;

        return $this;
    }

    /**
     * Show the action on the index view.
     *
     * @return $this
     */
    public function showOnIndex()
    {
        $this->showOnIndex = true;

        return $this;
    }

    /**
     * Show the action on the detail view.
     *
     * @return $this
     */
    public function showOnDetail()
    {
        $this->showOnDetail = true;

        return $this;
    }

    /**
     * Show the action on the table row.
     *
     * @deprecated
     *
     * @return $this
     */
    public function showOnTableRow()
    {
        return $this->showInline();
    }

    /**
     * Show the action on the table row.
     *
     * @return $this
     */
    public function showInline()
    {
        $this->showInline = true;

        return $this;
    }

    /**
     * Register a callback that should be invoked after the action is finished executing.
     *
     * @param  callable(\Illuminate\Support\Collection):mixed  $callback
     * @return $this
     */
    public function then($callback)
    {
        $this->thenCallback = $callback;

        return $this;
    }

    /**
     * Set the current batch ID being handled by the action.
     *
     * @param  string  $actionBatchId
     * @return $this
     */
    public function withActionBatchId(string $actionBatchId)
    {
        $this->actionBatchId = $actionBatchId;

        return $this;
    }

    /**
     * Register `then`, `catch`, and `finally` callbacks on the pending batch.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Bus\PendingBatch  $batch
     * @return void
     */
    public function withBatch(ActionFields $fields, PendingBatch $batch)
    {
        //
    }

    /**
     * Set the callback to be run to authorize running the action.
     *
     * @param  \Closure(\Laravel\Nova\Http\Requests\NovaRequest, mixed):bool  $callback
     * @return $this
     */
    public function canRun(Closure $callback)
    {
        $this->runCallback = $callback;

        return $this;
    }

    /**
     * Get the component name for the action.
     *
     * @return string
     */
    public function component()
    {
        return $this->component;
    }

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return $this->name ?: Nova::humanize($this);
    }

    /**
     * Get the URI key for the action.
     *
     * @return string
     */
    public function uriKey()
    {
        return Str::slug($this->name(), '-', null);
    }

    /**
     * Set the action to execute instantly.
     *
     * @return $this
     */
    public function withoutConfirmation()
    {
        $this->withoutConfirmation = true;

        return $this;
    }

    /**
     * Set the action to skip action events for models.
     *
     * @return $this
     */
    public function withoutActionEvents()
    {
        $this->withoutActionEvents = true;

        return $this;
    }

    /**
     * Determine if the action is to be shown on the index view.
     *
     * @return bool
     */
    public function shownOnIndex()
    {
        if ($this->onlyOnIndex == true) {
            return true;
        }

        if ($this->onlyOnDetail) {
            return false;
        }

        return $this->showOnIndex;
    }

    /**
     * Determine if the action is to be shown on the detail view.
     *
     * @return bool
     */
    public function shownOnDetail()
    {
        if ($this->onlyOnDetail) {
            return true;
        }

        if ($this->onlyOnIndex) {
            return false;
        }

        return $this->showOnDetail;
    }

    /**
     * Determine if the action is to be shown inline on the table row.
     *
     * @return bool
     */
    public function shownOnTableRow()
    {
        return $this->showInline;
    }

    /**
     * Set the text for the action's confirmation button.
     *
     * @param  string  $text
     * @return $this
     */
    public function confirmButtonText($text)
    {
        $this->confirmButtonText = $text;

        return $this;
    }

    /**
     * Set the text for the action's cancel button.
     *
     * @param  string  $text
     * @return $this
     */
    public function cancelButtonText($text)
    {
        $this->cancelButtonText = $text;

        return $this;
    }

    /**
     * Set the text for the action's confirmation message.
     *
     * @param  string  $text
     * @return $this
     */
    public function confirmText($text)
    {
        $this->confirmText = $text;

        return $this;
    }

    /**
     * Mark the action as a standalone action.
     *
     * @return $this
     */
    public function standalone()
    {
        $this->standalone = true;

        return $this;
    }

    /**
     * Determine if the action is a standalone action.
     *
     * @return bool
     */
    public function isStandalone()
    {
        return $this->standalone;
    }

    /**
     * Prepare the action for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        $request = app(NovaRequest::class);

        return array_merge([
            'cancelButtonText' => Nova::__($this->cancelButtonText),
            'component' => $this->component(),
            'confirmButtonText' => Nova::__($this->confirmButtonText),
            'confirmText' => Nova::__($this->confirmText),
            'destructive' => $this instanceof DestructiveAction,
            'name' => $this->name(),
            'uriKey' => $this->uriKey(),
            'fields' => FieldCollection::make($this->fields($request))
                ->filter->authorizedToSee($request)
                ->each->resolveForAction($request)
                ->applyDependsOnWithDefaultValues($request)
                ->all(),
            'showOnDetail' => $this->shownOnDetail(),
            'showOnIndex' => $this->shownOnIndex(),
            'showOnTableRow' => $this->shownOnTableRow(),
            'standalone' => $this->isStandalone(),
            'responseType' => $this->responseType,
            'withoutConfirmation' => $this->withoutConfirmation,
        ], $this->meta());
    }

    /**
     * Prepare the instance for serialization.
     *
     * @return array
     */
    public function __sleep()
    {
        $properties = (new ReflectionClass($this))->getProperties();

        return array_values(array_filter(array_map(function ($p) {
            return ($p->isStatic() || in_array($name = $p->getName(), ['runCallback', 'seeCallback', 'thenCallback'])) ? null : $name;
        }, $properties)));
    }
}
