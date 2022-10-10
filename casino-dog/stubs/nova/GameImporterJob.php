<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\URL;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Stack;
use Laravel\Nova\Fields\Line;
use Laravel\Nova\Fields\Textarea;
use Illuminate\Support\Facades\Cache;


class GameImporterJob extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\GameImporterJob::class;
    public static $group = 'Importer Tool';
    public static $showPollingToggle = true;
    public static $pollingInterval = 10;
    public static $polling = true;


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

    public static function authorizedToCreate(Request $request)
    {
        if(auth()->user()) {
            if($request->user()->is_admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        if(auth()->user()) {
            if($request->user()->is_admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public function authorizedToUpdate(Request $request)
    {
        if(auth()->user()) {
            if($request->user()->is_admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public static function detailQuery(NovaRequest $request, $query)
    {
        if(auth()->user()) {
            if($request->user()->is_admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if(auth()->user()) {
            if($request->user()->is_admin != '1') {
                return false;
            } else {
                return true;
            }
        } else {
          return false;
        }
    }

    public function get_host($url)
    {
        try {
        $url = urldecode($url);
        $parse = parse_url($url);
        $host = preg_replace('/^www\./', '', $parse['host']);
        return '<span class=\'font-medium\'>'.$host.'</span>';
        } catch(\Exception $e) {
            return '<span class=\'text-red-500\'>'.$e->getMessage().'</span>';
        }
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
            Stack::make('State', [
            Line::make('State', 'state', function () {
            return $this->get_host($this->link);
            })->asHtml(),
            Line::make('Link', 'link', function () {
                return '<small><a class=\'link-default\' href=\''.$this->link.'\'>'.substr($this->link, 0, 50).'...</a></small>';
                })->asHtml(),
            ])->hideWhenUpdating()->hideFromDetail(),
            URL::make('Link', 'link')->default('https://www.duxcasino.com/api/games/allowed_desktop')->hideFromIndex()->sortable()->required(),
            Text::make('Games Imported', 'imported_games')->hideFromIndex()->hideWhenCreating()->sortable(),
            Stack::make('Filter', [
                Line::make('Filter Key', 'filter_key', function () {
                return '<small>where</small> '.($this->filter_key ?? '-');
                })->asHtml(),
                Line::make('Filter Value', 'filter_value', function () {
                    return '<small>equals</small> '.($this->filter_value ?? '-');
                })->asHtml(),
                ])->hideWhenUpdating()->hideFromDetail(),
            Stack::make('State', [
            Line::make('State', 'state', function () {
            return $this->state;
            })->asHtml(),
            Line::make('Imported Games', 'imported_games', function () {
                return '<a class=\'link-default\' href=\'imported-games\'><small>'.substr($this->imported_games, 0, 50).' games imported</small></a>';
            })->asHtml(),
            ])->hideWhenUpdating()->hideFromDetail(),
            Boolean::make('Use Proxy', 'proxy'),
            Select::make('Schema', 'schema')
            ->required()
            ->default('softswiss')
            ->options([
                'softswiss' => 'Softswiss',
                'parimatch' => 'Parimatch',
            ]),

            new Panel('Filter Settings', $this->filterPanel()),
            new Panel('Job State', $this->statePanel()),

        ];
    }
    protected function filterPanel()
    {
        return [
        Text::make('Filter Key', 'filter_key')->placeholder('For example: provider ...')->hideFromIndex(),
        Text::make('Filter Value', 'filter_value')->placeholder('For example: bgaming ...')->hideFromIndex(),
        ];
    }

    protected function statePanel()
    {
        return [
            Text::make('State', 'state')->hideWhenCreating()->hideFromIndex(),
            Text::make('State Message', 'state_message')->hideWhenCreating()->hideFromIndex(),
            Textarea::make('Gamelist from Cache',
            function () {
                return $this->cache_result($this->cache_id($this->state_message));
            })->hideFromIndex()->hideWhenCreating(),
        ];
    }
    public function cache_id($state_message)
    {
        if (str_contains($state_message, 'game_importer_result')) {
            $cache_id = str_replace('Starting to process game now, response is cached under ID: ', '', $state_message);
            return $cache_id;
        } else {
            return '-';
        }
    }

    public function cache_result($id)
    {
        return json_encode(Cache::get($id), true);
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
        return [
            (new Actions\StartGameImport)->onlyOnTableRow()->showOnDetail()
            ->confirmText('Do you want to dispatch game import job? Make sure queue workers are running.')
            ->confirmButtonText('Start Job')
            ->cancelButtonText("Cancel"),
        ];
    }
}
