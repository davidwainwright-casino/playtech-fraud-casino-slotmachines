<?php

namespace Laravel\Nova\Actions;

use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Laravel\Nova\Contracts\BatchableAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Nova;

class DispatchAction
{
    /**
     * The request instance.
     *
     * @var \Laravel\Nova\Http\Requests\ActionRequest
     */
    protected $request;

    /**
     * The action instance.
     *
     * @var \Laravel\Nova\Actions\Action
     */
    protected $action;

    /**
     * The fields for action instance.
     *
     * @var \Laravel\Nova\Fields\ActionFields
     */
    protected $fields;

    /**
     * The pending batch instance (if the action implements BatchableAction).
     *
     * @var \Illuminate\Bus\PendingBatch|null
     */
    protected $batchJob;

    /**
     * Set dispatchable callback.
     *
     * @var (callable(\Laravel\Nova\Actions\Response):mixed)|null
     */
    protected $dispatchableCallback;

    /**
     * Create a new action dispatcher instance.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Laravel\Nova\Actions\Action  $action
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @return void
     */
    public function __construct(ActionRequest $request, Action $action, ActionFields $fields)
    {
        $this->request = $request;
        $this->action = $action;
        $this->fields = $fields;

        if ($action instanceof BatchableAction) {
            $this->configureBatchJob($action, $fields);
        }
    }

    /**
     * Configure the batch job for the action.
     *
     * @param  \Laravel\Nova\Actions\Action  $action
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @return void
     */
    protected function configureBatchJob(Action $action, ActionFields $fields)
    {
        $this->batchJob = tap(Bus::batch([]), function (PendingBatch $batch) use ($action, $fields) {
            $batch->name($action->name());

            if (! is_null($connection = $this->connection())) {
                $batch->onConnection($connection);
            }

            if (! is_null($queue = $this->queue())) {
                $batch->onQueue($queue);
            }

            $action->withBatch($fields, $batch);
        });
    }

    /**
     * Dispatch the action.
     *
     * @return \Laravel\Nova\Actions\Response
     *
     * @throws \Throwable
     */
    public function dispatch()
    {
        if ($this->action instanceof ShouldQueue) {
            return tap(new Response(), function ($response) {
                with($response, $this->dispatchableCallback);

                if (! is_null($this->batchJob)) {
                    $this->batchJob->dispatch();
                }

                return $response->successful();
            });
        }

        return with(new Response(), $this->dispatchableCallback);
    }

    /**
     * Dispatch the given action.
     *
     * @param  string  $method
     * @return $this
     *
     * @throws \Throwable
     */
    public function handleStandalone($method)
    {
        $this->dispatchableCallback = function (Response $response) use ($method) {
            if ($this->action instanceof ShouldQueue) {
                $this->addQueuedActionJob($method, collect());

                return;
            }

            return $response->successful([
                $this->dispatchSynchronouslyForCollection($method, collect()),
            ]);
        };

        return $this;
    }

    /**
     * Dispatch the given action.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  string  $method
     * @param  int  $chunkCount
     * @return $this
     *
     * @throws \Throwable
     */
    public function handleRequest(ActionRequest $request, $method, $chunkCount)
    {
        $this->dispatchableCallback = function (Response $response) use ($request, $method, $chunkCount) {
            if ($this->action instanceof ShouldQueue) {
                $request->chunks($chunkCount, function ($models) use ($request, $method) {
                    $models = $models->filterForExecution($request);

                    return $this->forModels($method, $models);
                });

                return;
            }

            $wasExecuted = false;

            $results = $request->chunks(
                $chunkCount, function ($models) use ($request, $method, &$wasExecuted) {
                    $models = $models->filterForExecution($request);

                    if (count($models) > 0) {
                        $wasExecuted = true;
                    }

                    return $this->forModels($method, $models);
                }
            );

            return $wasExecuted ? $response->successful($results) : $response->failed();
        };

        return $this;
    }

    /**
     * Dispatch the given action using custom handler.
     *
     * @param  \Laravel\Nova\Http\Requests\ActionRequest  $request
     * @param  \Closure(\Laravel\Nova\Http\Requests\ActionRequest, \Laravel\Nova\Actions\Response, \Laravel\Nova\Fields\ActionFields):\Laravel\Nova\Actions\Response  $callback
     * @return $this
     */
    public function handleUsing(ActionRequest $request, $callback)
    {
        $this->dispatchableCallback = function (Response $response) use ($request, $callback) {
            return $callback($request, $response, $this->fields);
        };

        return $this;
    }

    /**
     * Dispatch the given action.
     *
     * @param  string  $method
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed|void
     *
     * @throws \Throwable
     */
    public function forModels($method, Collection $models)
    {
        if ($this->action->isStandalone() || $models->isEmpty()) {
            return;
        }

        if ($this->action instanceof ShouldQueue) {
            $this->addQueuedActionJob($method, $models);

            return;
        }

        return $this->dispatchSynchronouslyForCollection($method, $models);
    }

    /**
     * Dispatch the given action synchronously for a model collection.
     *
     * @param  string  $method
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     *
     * @throws \Throwable
     */
    protected function dispatchSynchronouslyForCollection($method, Collection $models)
    {
        return Transaction::run(function ($batchId) use ($method, $models) {
            Nova::usingActionEvent(function ($actionEvent) use ($batchId, $models) {
                if (! $this->action->withoutActionEvents) {
                    $actionEvent->createForModels(
                        $this->request, $this->action, $batchId, $models
                    );
                }
            });

            return $this->action->withActionBatchId($batchId)->{$method}($this->fields, $models);
        }, function ($batchId) {
            Nova::usingActionEvent(function ($actionEvent) use ($batchId) {
                $actionEvent->markBatchAsFinished($batchId);
            });
        });
    }

    /**
     * Dispatch the given action to the queue for a model collection.
     *
     * @param  string  $method
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     *
     * @throws \Throwable
     */
    protected function addQueuedActionJob($method, Collection $models)
    {
        return Transaction::run(function ($batchId) use ($method, $models) {
            Nova::usingActionEvent(function ($actionEvent) use ($batchId, $models) {
                if (! $this->action->withoutActionEvents) {
                    $actionEvent->createForModels(
                        $this->request, $this->action, $batchId, $models, 'waiting'
                    );
                }
            });

            $job = new CallQueuedAction(
                $this->action, $method, $this->request->resolveFields(), $models, $batchId
            );

            if ($this->action instanceof BatchableAction) {
                $this->batchJob->add([$job]);

                $this->batchJob->options['resourceIds'] = array_values(array_unique(array_merge(
                    $this->batchJob->options['resourceIds'] ?? [],
                    $models->map(function ($model) {
                        return $model->getKey();
                    })->all()
                )));
            } else {
                Queue::connection($this->connection())->pushOn(
                    $this->queue(), $job
                );
            }
        });
    }

    /**
     * Extract the queue connection for the action.
     *
     * @return string|null
     */
    protected function connection()
    {
        return property_exists($this->action, 'connection') ? $this->action->connection : null;
    }

    /**
     * Extract the queue name for the action.
     *
     * @return string|null
     */
    protected function queue()
    {
        return property_exists($this->action, 'queue') ? $this->action->queue : null;
    }
}
