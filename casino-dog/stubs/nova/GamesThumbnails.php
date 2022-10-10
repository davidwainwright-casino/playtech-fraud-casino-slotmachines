<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\URL;

class GamesThumbnails extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\GamesThumbnails::class;
    public static $group = 'Production';
    public static $showPollingToggle = true;
    public static $pollingInterval = 10;
    public static $name = 'Games';
    public static $tableStyle = 'tight';
    public static $clickAction = 'preview';
    public static $perPageOptions = [50, 100, 150];

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
        'id',
    ];



    public static function label()
    {
        return 'Thumbnails';
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
            ID::make()->sortable(),
            BelongsTo::make('Gameslist')->rules('required', 'max:50', 'min:1')
            ->readonly(function ($request) {
                    return $request->user()->is_admin != "1";
            }),
            Text::make('Image Group', 'img_ext')->sortable(),

            Text::make('Game ID', 'img_gid')->hideFromIndex()->sortable(),
            Stack::make('State', [
                Line::make('Link', 'img_url', function () {
                return '<small><a class=\'link-default\' href=\''.$this->img_url.'\'>'.substr($this->img_url, 0, 50).'...</a></small>';
                })->asHtml(),
                Line::make('Avatar', 'img_gid', function () {
                    return $this->img_gid;
                    })->asHtml(),
                ])->hideWhenUpdating()->hideFromDetail(),
            URL::make('Image Url', 'img_url')->hideFromIndex()->sortable()->required(),

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
