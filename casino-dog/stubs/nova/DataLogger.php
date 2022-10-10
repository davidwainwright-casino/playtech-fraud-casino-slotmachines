<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;

class DataLogger extends Resource
{

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }


    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\DataLogger::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';
    public static $tableStyle = 'tight';
    public static $clickAction = 'preview';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
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
            ID::make()->sortable()->textAlign('left'),
            Text::make('Log', 'data', function () {
                return '<small>' . substr($this->get_datalog(), 0, 35).'</a></small>';
            })->asHtml()->textAlign('left'),
            Text::make('Function Type', 'type')->sortable()->textAlign('center'),
            DateTime::make('Time', 'created_at')->textAlign('right'),

            /* Preview Modal */
            Text::make('Log Data',
            function () {
                return $this->get_datalog_full();
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),
            Text::make('Extra Data',
            function () {
                return $this->extra_data;
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),
        ];
    }

    public function get_datalog()
    {
        if(isset($this->data['message'])) {
            return substr($this->data['message'], 0, 35);
        } else {
            return '<small>' . substr($this->data, 0, 35). '</small>';
        }
    }

    public function get_datalog_full()
    {
        if(isset($this->data['message'])) {
            return $this->data['message'];
        } else {
            return $this->data;
        }
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
