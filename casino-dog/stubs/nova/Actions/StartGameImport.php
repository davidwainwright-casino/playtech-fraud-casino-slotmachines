<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Notifications\Notifiable;

class StartGameImport extends Action
{
    use InteractsWithQueue, Queueable, Notifiable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if(auth()->user()) {
            if(auth()->user()->is_admin != '1') {
                return Action::danger('Only admins are allowed to launch import jobs.');
            }
        }

        if($models->count() > 1) {
            return Action::danger('Please run this on only one batch at a time.');
        }

        $selectModel = $models->first();

        if($selectModel->state === 'JOB_HEALTH_PASSED') {
            return Action::danger('Previous job is in progress.');
        }

        $selectModel->update([
            'imported_games' => 0,
        ]);

        $kernel = new \Wainwright\CasinoDog\Models\GameImporterJob;
        $dispatch = $kernel->start_job($selectModel->id);

        return Action::message('Dispatched job, result will be available on \'Imported Games\' resource.');
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
