<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class iSoftbetRawIdentifierFromDemo extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */


    public $name = 'Job: [iSoftbet] Get & Store Identifier';


    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() === 1) {
            $selectModel = $models->first();
            if($selectModel->provider !== 'isoftbet') {
                Action::danger('This job only applies to iSoftbet games.');
            }
            if($selectModel->demolink !== 0) {
                    \Wainwright\CasinoDog\Controllers\Game\iSoftbet\Jobs\GetRawIdentifierFromDemo::dispatch($selectModel->gid, $selectModel->demolink);
            }
        } else {
            foreach($models as $selectModel) {
                if($selectModel->provider !== 'isoftbet') {
                    Action::danger('This job only applies to iSoftbet games.');
                }
                if($selectModel->demolink !== 0) {
                    \Wainwright\CasinoDog\Controllers\Game\iSoftbet\Jobs\GetRawIdentifierFromDemo::dispatch($selectModel->gid, $selectModel->demolink);
                }
            }
        }

        return Action::message('Dispatched job batch.');
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
