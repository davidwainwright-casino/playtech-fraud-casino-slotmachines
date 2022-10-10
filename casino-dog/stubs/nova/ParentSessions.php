<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Boolean;
class ParentSessions extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\ParentSessions::class;
    public static $group = 'Production';
    public static $showPollingToggle = true;
    public static $pollingInterval = 10;
    public static $name = 'Parent Sessions';
    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'player_id', 'player_operator_id', 'game_id'
    ];


    public static function label()
    {
        return 'Parent Sessions';
    }


    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->hideFromIndex()->sortable(),
            Text::make('Token Internal', 'token_internal')->hideFromIndex(),
            Text::make('Token Internal', 'token_internal', function () {
                return '<small>' . $this->token_internal . '</small>';
            })->asHtml()->hideWhenUpdating()->hideFromDetail(),
            Text::make('State', 'state'),
            Boolean::make('Expired', 'expired_bool'),
            Stack::make('Player', [
                Line::make('Player', 'player_id', function () {
                    return 'Internal: ' . $this->player_id;
                })->asSmall(),
                Line::make('Player', 'player_operator_id', function () {
                    return 'Operator: ' . $this->player_operator_id;
                })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),
            Stack::make('Game', [
                Line::make('Game', 'game_id', function () {
                    return '<span class=\'inertia-link-active\'>' . $this->game_id . '</span>';
                })->asHtml(),
                Line::make('Game Provider', 'game_provider', function () {
                    return 'Provider: ' . $this->game_provider;
                })->asSmall(),
            ])->hideWhenUpdating()->hideFromDetail(),


            Text::make('Game Slug', 'game_id')->hideFromIndex(),
            Text::make('Game Provider', 'game_provider')->hideFromIndex(),
            Text::make('Internal Player ID', 'player_id')->hideFromIndex(),
            Text::make('Operator Player ID', 'player_operator_id')->hideFromIndex(),
            Text::make('Currency', 'currency'),
            Boolean::make('Game Token', 'token_original')
            ->falseValue(0)
            ->trueValue(!NULL),
            Boolean::make('Game Bridged', 'token_original_bridge')
            ->falseValue(0)
            ->trueValue(!NULL),
            Text::make('Game Token', 'token_original')->sortable()->hideFromIndex(),
            Text::make('Game Token Bridge', 'token_original_bridge')->hideFromIndex()->sortable(),
        ];
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
