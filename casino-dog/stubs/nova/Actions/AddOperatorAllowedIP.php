<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Heading;

class AddOperatorAllowedIP extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public $name = 'Change IP on allowed list';


    public function handle(ActionFields $fields, Collection $models)
    {
        if ($models->count() > 1) {
            return Action::danger('Please run this on only one resource model per time.');
        }

        $selectModel = $models->first();

        if (auth()->user()->id != $selectModel->ownedBy) {
            if(auth()->user()->is_admin != '1') {
                return Action::danger('You are not authorized to add new IP to whitelist.');
            }
        }
        $ip = $fields->ip;


        if(filter_var($ip, FILTER_VALIDATE_IP)) {
        $models->first()->update(['operator_access' => $ip]);
        return Action::message('Added IP to whitelist.');

        } else {
        return Action::danger('Does not seem a valid IP address, make sure to add only IPV4 IPs.');
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

        return [
            Heading::make('<p>Enter IP of your server below. Please note you can not add more then 4 IPs per API key, for adding more IPs please contact support.</p><br><p><b>Only IPV4 is supported.</b></p>')->asHtml(),

            Text::make('IP')->placeholder($request->ip())->rules('required', 'min:5', 'max:66'),

        ];
    }

}
