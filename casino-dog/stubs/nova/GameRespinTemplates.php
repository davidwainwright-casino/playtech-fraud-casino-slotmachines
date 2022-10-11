<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;

class GameRespinTemplates extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\GameRespinTemplate::class;
    public static $name = 'Respin Templates';
    public static $group = 'Production';
    public static $showPollingToggle = true;
    public static $pollingInterval = 10;
    public static $polling = true;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'gid';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'gid',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Game ID', 'gid')->sortable(),
            Text::make('Data', 'game_data')->hideFromIndex(),
            Text::make('Log', 'game_data', function () {
                return $this->get_game_data();
            })->asHtml()->textAlign('left'),
            Text::make('Type', 'game_type')->sortable(),


        ];
    }


    public function get_game_data()
    {
        return '<small>' . substr($this->game_data, 0, 35). '</small>';
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
