<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Panel;
use Laravel\Nova\Fields\Password;

use Illuminate\Support\Str;


class OperatorAccess extends Resource
{
    public static $group = 'Account';


    public static function detailQuery(NovaRequest $request, $query)
    {
        if($request->user()->is_admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        if($request->user()->is_admin != '1') {
            return $query->where('ownedBy', $request->user()->id);
        } else {
            return $query;
        }
    }

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
        return false;
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

    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Wainwright\CasinoDog\Models\OperatorAccess::class;

    public static function perPageOptions()
    {
        return [50, 100, 150, 250, 500];
    }

    public static function label()
    {
        return 'Operator Access';
    }

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
            Text::make('Operator Key', 'operator_key')->default(md5(now().time()))->sortable(),
            Password::make('Secret Password', 'operator_secret')
            ->sortable()
            ->readonly()
            ->hideFromIndex()
            ->help('Go to index page and select action to regenerate new secret key.')
            ->rules('required', 'max:32', 'min:3')
            ->default(Str::random(12))->withMeta(['extraAttributes' => ['type' => 'password']]),
            Text::make('Allowed IP', 'operator_access')
            ->hideFromIndex()
            ->help('Add IP from action overview. Read documentation for guidance to add IPs.')
            ->default('127.0.0.1')
            ->readonly()
            ->rules('required', 'max:128', 'min:3'),
            Text::make('Callback URL', 'callback_url')->sortable(),
            Boolean::make('Active', 'active')->sortable()->default(1),
            BelongsTo::make('User')->rules('required', 'max:50', 'min:1')
            ->hideFromIndex(function ($request) {
                    return $request->user()->is_admin != "1";
            })
            ->readonly(function ($request) {
                    return $request->user()->is_admin != "1";
            }),


            new Panel('Time', $this->datetimePanel()),

        ];
    }

    protected function datetimePanel()
    {
        return [
            DateTime::make('Last used at', 'last_used_at')->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable(),
            DateTime::make('Created at', 'created_at')->hideFromIndex()->default(time())->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable(),
            DateTime::make('Updated at', 'updated_at')->hideFromIndex()->default(time())->hideWhenUpdating()->hideWhenCreating()->readonly()->sortable(),

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
        return [
            (new Actions\GenerateOperatorSecret)->onlyOnTableRow()->showOnDetail()
            ->confirmText(' Do you want generate new operator secret? Operator secret is only show once. It will immediately invalidate the old operator secret for any use.')
            ->confirmButtonText('Activate the above new secret key')
            ->cancelButtonText("Cancel"),
            (new Actions\AddOperatorAllowedIP)->onlyOnTableRow()->showOnDetail()
            ->confirmButtonText('Add IP to allowed list')
            ->cancelButtonText("Cancel"),
            (new Actions\OperatorSendPing)->onlyOnTableRow()->showOnDetail()
            ->confirmText('Do you want to send a ping to operator endpoint?')
            ->confirmButtonText('Ping')
            ->cancelButtonText("Cancel"),
        ];
    }
}
