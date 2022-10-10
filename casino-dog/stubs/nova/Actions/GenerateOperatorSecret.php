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


class GenerateOperatorSecret extends Action
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
        if ($models->count() > 1) {
            return Action::danger('Please run this on only one user resource.');
        }

            $selectModel = $models->first();

        if (auth()->user()->id != $selectModel->ownedBy) {
            if(auth()->user()->is_admin != '1') {
                return Action::danger('You are not authorized to truncate IP whitelist.');
            }
        }

        $token = $fields->secret;

        $models->first()->update(['operator_secret' => $token]);

        return Action::message('API secret password has been changed.');
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
            Heading::make('<p>Please note that below secret key is <b>only shown once</b>, after you confirm this action your old secret key will be invalidated.</p><br>We are unable to retrieve your old secret keys or current secret keys as they are hashed, so make sure to save it in a secure place before you press confirm.<p</p>')->asHtml(),
            Text::make('Secret Key', 'secret')->readonly()->help('Make sure to copy this key.')->default(Str::random(12))->rules('required', 'min:4', 'max:15'),
        ];
    }

}

