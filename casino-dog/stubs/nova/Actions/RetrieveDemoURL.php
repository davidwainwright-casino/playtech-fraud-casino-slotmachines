<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class RetrieveDemoURL extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public $name = 'Job: [All] Get Demo URL';


    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() === 1) {
            $selectModel = $models->first();
            \Wainwright\CasinoDog\Jobs\RetrieveRealDemoURL::dispatch($selectModel->gid);
        } else {
            foreach($models as $selectModel) {
                \Wainwright\CasinoDog\Jobs\RetrieveRealDemoURL::dispatch($selectModel->gid);
            }
        }
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
}
