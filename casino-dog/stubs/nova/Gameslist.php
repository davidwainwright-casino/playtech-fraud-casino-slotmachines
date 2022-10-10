<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Boolean;

class Gameslist extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\Gameslist::class;
    public static $group = 'Production';
    public static $showPollingToggle = true;
    public static $pollingInterval = 10;
    public static $name = 'Games';
    public static $tableStyle = 'tight';
    public static $clickAction = 'preview';
    public static $perPageOptions = [50, 100, 150];

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToReplicate(Request $request)
    {
        return false;
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';


    public static function label()
    {
        return 'Gameslist';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'gid', 'name', 'provider',
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
            Text::make('Img',
            function () {
                return $this->thumbnail_link($this->gid, $this->image, $this->source_schema);
            })->asHtml()->hideFromDetail(),
            ID::make()->sortable(),
            Text::make('Game ID', 'gid')->sortable(),
            Text::make('Name', 'name')->sortable(),
            Text::make('Provider', 'provider')->sortable(),
            Text::make('Source', 'source')->sortable(),
            Text::make('Schema', 'source_schema')->sortable(),
            Text::make('Image', 'image')->hideFromIndex()->sortable(),
            Text::make('Batch', 'batch')->hideFromIndex()->sortable(),

            Boolean::make('Source Demolink', 'demolink')
            ->falseValue(NULL)
            ->trueValue(!NULL),
            DateTime::make('Time', 'created_at')->readonly()->sortable(),

            /*Preview */
            Text::make('Origin Demolink',
            function () {
                return '<a target=\'_blank\' class=\'link-default\' href=\''.$this->demolink.'\'>'.$this->demolink.'</a>';
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),
            Text::make('Create Session',
            function () {
                return $this->build_session_url();
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),
            Text::make('Create Session and Redirect to Game',
            function () {
                return $this->build_session_and_redirect_url();
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),
            Text::make('Additional',
            function () {
                return $this->gid_extra;
            })->asHtml()->hideWhenCreating()->hideWhenUpdating()->hideFromIndex()->hideFromDetail()->showOnPreview(),



        ];
    }
    public function find_operatorkey()
    {
        $find_operator_key = \Wainwright\CasinoDog\Models\OperatorAccess::where('ownedBy', auth()->user()->id)->where('active', 1)->first();
        if($find_operator_key) {
          return $find_operator_key->operator_key;
        } else {
          return false;
        }
    }

    public function build_session_url() {
        $key = $this->find_operatorkey();
        if($key === false) {
            return 'no active operator key attached to your account';
        }
        $url = env('APP_URL').'/api/createSession?game='.$this->slug.'&player='.auth()->user()->name.'&currency=USD&operator_key='.$this->find_operatorkey().'&mode=real';
        return '<a target=\'_blank\' class=\'link-default\' href=\''.$url.'\'>'.$url.'</a>';
    }

    public function build_session_and_redirect_url() {
        $key = $this->find_operatorkey();
        if($key === false) {
            return 'no active operator key attached to your account';
        }
        $url = env('APP_URL').'/api/createSessionAndRedirect?game='.$this->slug.'&player='.auth()->user()->name.'&currency=USD&operator_key='.$this->find_operatorkey().'&mode=real';
        return '<a target=\'_blank\' class=\'link-default\' href=\''.$url.'\'>'.$url.'</a>';
    }



    public function demolink_status($demolink)
    {
        if($demolink === NULL) {
            return 'On';
        } else {
            return 'Off';
        }
    }

    public function thumbnail_link($gid, $image, $source_schema)
    {
        if($image !== NULL) {
            if (filter_var($image, FILTER_VALIDATE_URL)) {
            return "<img style='border-radius: 50%; border-width: 2px; border-color: #5488fa;' width='27px' height='27px' src='".$image."' >";
            }
        }
        $source_schema = strtolower($source_schema);
        if($source_schema === 'parimatch') {
            $url = 'https://parimatch.co.tz/service-discovery/service/pm-casino/img/tr:n-slots_game_image_desktop/Casino/eva/games/'.$gid;
            return "<img style='border-radius: 50%; border-width: 2px; border-color: transparent;' width='27px' height='27px' src='".$url.".png' >";
        } elseif($source_schema === 'softswiss') {
            //fallback url
            $explode_gid = explode('/', $gid);
            $provider = $explode_gid[0];
            $game_id = $explode_gid[1];
            $fallback_url = config('casino-dog.s3_image_store.fallback_image_source').$provider.'/'.$game_id.'.png';
            return "<img style='border-radius: 50%; border-width: 2px; border-color: transparent;' width='27px' height='27px' src='".$fallback_url."' >";
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
        return [
            new Filters\SourcedDemourl,
            new Filters\UploadedImageS3,
            new Filters\ProviderFilter,

        ];
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
            (new Actions\RetrieveDemoURL)->showInline()->showOnIndex()->showOnDetail()
            ->confirmText('This will retrieve & check real demo URL. Please note that even if this job fails, it will overwrite previous demo URL field in database.')
            ->confirmButtonText('Start Job')
            ->cancelButtonText("Cancel"),
            (new Actions\StoreImageS3)->showInline()->showOnIndex()->showOnDetail()
            ->confirmButtonText('Start Job')
            ->cancelButtonText("Cancel"),
            (new Actions\iSoftbetRawIdentifierFromDemo)->showInline()->showOnIndex()->showOnDetail()
            ->confirmText('iSoftbet retrieve direct game id from Softswiss demo session launcher.')
            ->confirmButtonText('Start Job')
            ->cancelButtonText("Cancel"),
        ];
    }
}
