<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImportedGame extends Action implements ShouldQueue
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
        if ($models->count() === 1) {
            $selectModel = $models->first();
            \Wainwright\CasinoDog\Jobs\TransferToGamelist::dispatch($selectModel->gid);
        } else {
            foreach($models as $selectModel) {
                \Wainwright\CasinoDog\Jobs\TransferToGamelist::dispatch($selectModel->gid);
            }
        }

        return Action::message('Started dispatch job. Result will be within the \'Active Games\' resource.');
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
