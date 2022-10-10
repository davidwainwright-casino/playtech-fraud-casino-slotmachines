<?php

namespace Laravel\Nova\Actions;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Nova\Nova;

trait CallsQueuedActions
{
    use Batchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The action class name.
     *
     * @var \Laravel\Nova\Actions\Action
     */
    public $action;

    /**
     * The method that should be called on the action.
     *
     * @var string
     */
    public $method;

    /**
     * The resolved fields.
     *
     * @var \Laravel\Nova\Fields\ActionFields
     */
    public $fields;

    /**
     * The batch ID of the action event records.
     *
     * @var string
     */
    public $actionBatchId;

    /**
     * Call the action using the given callback.
     *
     * @param  callable(\Laravel\Nova\Actions\Action):void  $callback
     * @return void
     */
    protected function callAction($callback)
    {
        Nova::usingActionEvent(function ($actionEvent) {
            $actionEvent->markBatchAsRunning($this->actionBatchId);
        });

        $action = $this->setJobInstanceIfNecessary($this->action);

        $callback($action);

        if (! $this->job->hasFailed() && ! $this->job->isReleased()) {
            Nova::usingActionEvent(function ($actionEvent) {
                $actionEvent->markBatchAsFinished($this->actionBatchId);
            });
        }
    }

    /**
     * Set the job instance of the given class if necessary.
     *
     * @param  mixed  $instance
     * @return mixed
     */
    protected function setJobInstanceIfNecessary($instance)
    {
        if (in_array(InteractsWithQueue::class, class_uses_recursive(get_class($instance)))) {
            $instance->setJob($this->job);
        }

        return $instance;
    }

    /**
     * Get the display name for the queued job.
     *
     * @return string
     */
    public function displayName()
    {
        return get_class($this->action);
    }
}
