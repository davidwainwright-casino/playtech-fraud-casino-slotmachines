<?php

namespace Laravel\Nova\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Nova;

class CallQueuedAction
{
    use CallsQueuedActions;

    /**
     * The Eloquent model/data collection.
     *
     * @var \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public $models;

    /**
     * Create a new job instance.
     *
     * @param  \Laravel\Nova\Actions\Action  $action
     * @param  string  $method
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @param  string  $actionBatchId
     * @return void
     */
    public function __construct(Action $action, $method, ActionFields $fields, Collection $models, $actionBatchId)
    {
        $this->action = $action;
        $this->method = $method;
        $this->fields = $fields;
        $this->models = $models;
        $this->actionBatchId = $actionBatchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->callAction(function ($action) {
            return $action->withActionBatchId($this->actionBatchId)->{$this->method}($this->fields, $this->models);
        });
    }

    /**
     * Call the failed method on the job instance.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function failed($e)
    {
        Nova::usingActionEvent(function ($actionEvent) use ($e) {
            $actionEvent->markBatchAsFailed($this->actionBatchId, $e);
        });

        if ($method = $this->failedMethodName()) {
            call_user_func([$this->action, $method], $this->fields, $this->models, $e);
        }
    }

    /**
     * Get the name of the "failed" method that should be called for the action.
     *
     * @return string|null
     */
    protected function failedMethodName()
    {
        if (($method = $this->failedMethodForModel()) &&
            method_exists($this->action, $method)) {
            return $method;
        }

        return method_exists($this->action, 'failed')
                    ? 'failed' : null;
    }

    /**
     * Get the appropriate "failed" method name for the action's model type.
     *
     * @return string|null
     */
    protected function failedMethodForModel()
    {
        if ($this->models->isNotEmpty()) {
            return 'failedFor'.Str::plural(class_basename($this->models->first()));
        }
    }
}
