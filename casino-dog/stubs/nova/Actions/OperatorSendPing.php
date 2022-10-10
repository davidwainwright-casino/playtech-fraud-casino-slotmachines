<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class OperatorSendPing extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        if($models->count() > 1) {
            return Action::danger('Please run this on only one operator at a time.');
        }
    
        $selectModel = $models->first();
        $operator_ping = \Wainwright\CasinoDog\Controllers\Game\OperatorsController::operatorPing($selectModel->operator_key, $selectModel->operator_access);
        if($operator_ping === false) {
            return Action::danger('Ping seemed to have failed, check Datalogger resource for the reason.');
        } else {
            return Action::message('Seems operator is correctly configured. Proceed to create a game from within the gameslist resource.');
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
