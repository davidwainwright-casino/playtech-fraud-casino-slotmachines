<?php

namespace Laravel\Nova;

use BadMethodCallException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Exceptions\ResourceMissingException;
use Laravel\Nova\Http\Middleware\RedirectIfAuthenticated;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Menu\Menu;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Nova
{
    use AuthorizesRequests;
    use Concerns\HandlesRoutes;
    use Concerns\InteractsWithActionEvent;
    use Concerns\InteractsWithEvents;

    /**
     * The registered dashboard names.
     *
     * @var array<int, \Laravel\Nova\Dashboard>
     */
    public static $dashboards = [];

    /**
     * The registered resource names.
     *
     * @var array<int, class-string<\Laravel\Nova\Resource>>
     */
    public static $resources = [];

    /**
     * An index of resource names keyed by the model name.
     *
     * @var array<class-string<\Illuminate\Database\Eloquent\Model>, class-string<\Laravel\Nova\Resource>>
     */
    public static $resourcesByModel = [];

    /**
     * The callback used to create new users via the CLI.
     *
     * @var (\Closure(string, string, string):\Illuminate\Database\Eloquent\Model)|null
     */
    public static $createUserCallback;

    /**
     * The callback used to gather new user information via the CLI.
     *
     * @var (\Closure(\Illuminate\Console\Command):array)|null
     */
    public static $createUserCommandCallback;

    /**
     * The callable that resolves the user's timezone.
     *
     * @var (\Closure(\Illuminate\Http\Request):?string)|null
     */
    public static $userTimezoneCallback;

    /**
     * All of the registered Nova tools.
     *
     * @var array<int, \Laravel\Nova\Tool>
     */
    public static $tools = [];

    /**
     * All of the registered Nova tool scripts.
     *
     * @var array<int, \Laravel\Nova\Script>
     */
    public static $scripts = [];

    /**
     * All of the registered Nova tool CSS.
     *
     * @var array<int, \Laravel\Nova\Style>
     */
    public static $styles = [];

    /**
     * The variables that should be made available on the Nova JavaScript object.
     *
     * @var array<string, mixed>
     */
    public static $jsonVariables = [];

    /**
     * The callback used to report Nova's exceptions.
     *
     * @var (\Closure(\Throwable):void)|(callable(\Throwable):void)|null
     */
    public static $reportCallback;

    /**
     * Indicates if Nova should register its migrations.
     *
     * @var bool
     */
    public static $runsMigrations = true;

    /**
     * The translations that should be made available on the Nova JavaScript object.
     *
     * @var array<string, string>
     */
    public static $translations = [];

    /**
     * The callback used to sort Nova resources in the sidebar.
     *
     * @var (\Closure(string):mixed)|null
     */
    public static $sortCallback;

    /**
     * The debounce amount to use when using global search.
     *
     * @var float
     */
    public static $debounce = 0.5;

    /**
     * The callback used to create Nova's main menu.
     *
     * @var (\Closure(\Illuminate\Http\Request, \Laravel\Nova\Menu\Menu):\Laravel\Nova\Menu\Menu|array)|null
     */
    public static $mainMenuCallback;

    /**
     * The callback used to create Nova's user menu.
     *
     * @var (\Closure(\Illuminate\Http\Request, \Laravel\Nova\Menu\Menu):\Laravel\Nova\Menu\Menu|array)|null
     */
    public static $userMenuCallback;

    /**
     * The callback used to resolve Nova's footer.
     *
     * @var (\Closure(\Illuminate\Http\Request):string)|null
     */
    public static $footerCallback;

    /**
     * The callback used to resolve Nova's RTL.
     *
     * @var (\Closure():bool)|bool|null
     */
    public static $rtlCallback;

    /**
     * The initial path Nova should route to when visiting the base.
     *
     * @var string
     */
    public static $initialPath = '/dashboards/main';

    /**
     * Indicates if Nova is being used to authenticate users.
     *
     * @var bool
     */
    public static $withAuthentication = false;

    /**
     * Indicates if Nova is being used to reset passwords.
     *
     * @var bool
     */
    public static $withPasswordReset = false;

    /**
     * The interval (in seconds) to poll for new Nova notifications.
     *
     * @var int
     */
    public static $notificationPollingInterval = 7;

    /**
     * Indicates if Nova's global search is enabled.
     *
     * @var bool
     */
    public static $withGlobalSearch = true;

    /**
     * Indicates if Nova's notification center is enabled.
     *
     * @var bool
     */
    public static $withNotificationCenter = true;

    /**
     * Indicates if Nova's light/dark mode switcher is enabled.
     *
     * @var bool
     */
    public static $withThemeSwitcher = true;

    /**
     * Get the current Nova version.
     *
     * @return string
     */
    public static function version()
    {
        return Cache::driver('array')->rememberForever('nova.version', function () {
            $manifest = json_decode(File::get(__DIR__.'/../composer.json'), true);

            $version = $manifest['version'] ?? '4.x';

            return $version.' (Honest Man)';
        });
    }

    /**
     * Get the app name utilized by Nova.
     *
     * @return string
     */
    public static function name()
    {
        return config('nova.name', 'Nova Site');
    }

    /**
     * Run callback when currently serving Nova.
     *
     * @param  callable(\Laravel\Nova\Http\Requests\NovaRequest):mixed  $callback
     * @param  (callable(\Illuminate\Http\Request):mixed)|null  $default
     * @return mixed
     */
    public static function whenServing(callable $callback, callable $default = null)
    {
        if (app()->bound(NovaRequest::class)) {
            return $callback(app()->make(NovaRequest::class));
        }

        if (is_callable($default)) {
            return $default(app('request'));
        }
    }

    /**
     * Get current user using `nova.guard`.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return \Illuminate\Foundation\Auth\User|null
     */
    public static function user(Request $request = null)
    {
        $guard = config('nova.guard');

        if (is_null($request)) {
            return call_user_func(app('auth')->userResolver(), $guard);
        }

        return $request->user($guard);
    }

    /**
     * Register the Nova routes.
     *
     * @return \Laravel\Nova\PendingRouteRegistration
     */
    public static function routes()
    {
        Route::aliasMiddleware('nova.guest', RedirectIfAuthenticated::class);

        return new PendingRouteRegistration();
    }

    /**
     * Enable Nova's authentication functionality.
     *
     * @return static
     */
    public static function withAuthentication()
    {
        static::$withAuthentication = true;

        return new static();
    }

    /**
     * Enable Nova's password reset functionality.
     *
     * @return static
     */
    public static function withPasswordReset()
    {
        static::$withPasswordReset = true;

        return new static();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function resourcesForNavigation(Request $request)
    {
        return static::authorizedResources($request)
            ->availableForNavigation($request)
            ->sortBy(static::sortResourcesWith())
            ->all();
    }

    /**
     * Return Nova's authorized resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\ResourceCollection
     */
    public static function authorizedResources(Request $request)
    {
        return static::resourceCollection()->authorized($request);
    }

    /**
     * Return the base collection of Nova resources.
     *
     * @return \Laravel\Nova\ResourceCollection
     */
    private static function resourceCollection()
    {
        return ResourceCollection::make(static::$resources);
    }

    /**
     * Get the sorting strategy to use for Nova resources.
     *
     * @return \Closure(string):mixed
     */
    public static function sortResourcesWith()
    {
        return static::$sortCallback ?? function ($resource) {
            return $resource::label();
        };
    }

    /**
     * Replace the registered resources with the given resources.
     *
     * @param  array<int, class-string<\Laravel\Nova\Resource>>  $resources
     * @return static
     */
    public static function replaceResources(array $resources)
    {
        static::$resources = $resources;

        return new static();
    }

    /**
     * Get the available resource groups for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    public static function groups(Request $request)
    {
        return collect(static::availableResources($request))
            ->map(function ($item, $key) {
                return $item::group();
            })->unique()->values();
    }

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, class-string<\Laravel\Nova\Resource>>
     */
    public static function availableResources(Request $request)
    {
        return static::authorizedResources($request)
            ->sortBy(static::sortResourcesWith())
            ->all();
    }

    /**
     * Get the grouped resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, \Laravel\Nova\ResourceCollection<int, class-string<\Laravel\Nova\Resource>>>
     */
    public static function groupedResources(Request $request)
    {
        return ResourceCollection::make(static::availableResources($request))
            ->grouped()
            ->all();
    }

    /**
     * Get the grouped resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\ResourceCollection<string, \Laravel\Nova\ResourceCollection<int, class-string<\Laravel\Nova\Resource>>>
     */
    public static function groupedResourcesForNavigation(Request $request)
    {
        return ResourceCollection::make(static::availableResources($request))
            ->groupedForNavigation($request)
            ->filter->count();
    }

    /**
     * Register all of the resource classes in the given directory.
     *
     * @param  string  $directory
     * @return void
     */
    public static function resourcesIn($directory)
    {
        $namespace = app()->getNamespace();

        $resources = [];

        foreach ((new Finder())->in($directory)->files() as $resource) {
            $resource = $namespace.str_replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($resource->getPathname(), app_path().DIRECTORY_SEPARATOR)
            );

            if (
                is_subclass_of($resource, Resource::class) &&
                ! (new ReflectionClass($resource))->isAbstract() &&
                ! (is_subclass_of($resource, ActionResource::class))
            ) {
                $resources[] = $resource;
            }
        }

        static::resources(
            collect($resources)->sort()->all()
        );
    }

    /**
     * Register the given resources.
     *
     * @param  array<int, class-string<\Laravel\Nova\Resource>>  $resources
     * @return static
     */
    public static function resources(array $resources)
    {
        static::$resources = array_unique(
            array_merge(static::$resources, $resources)
        );

        return new static();
    }

    /**
     * Get a new resource instance with the given model instance.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Laravel\Nova\Resource<\Illuminate\Database\Eloquent\Model>
     *
     * @throws \Laravel\Nova\Exceptions\ResourceMissingException
     */
    public static function newResourceFromModel($model)
    {
        if (is_null($resource = static::resourceForModel($model))) {
            throw new ResourceMissingException($model);
        }

        return new $resource($model);
    }

    /**
     * Get the resource class name for a given model class.
     *
     * @param  \Illuminate\Database\Eloquent\Model|class-string<\Illuminate\Database\Eloquent\Model>  $class
     * @return class-string<\Laravel\Nova\Resource>|null
     */
    public static function resourceForModel($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (isset(static::$resourcesByModel[$class])) {
            return static::$resourcesByModel[$class];
        }

        $resource = static::resourceCollection()->first(function ($value) use ($class) {
            return $value::$model === $class;
        });

        return static::$resourcesByModel[$class] = $resource;
    }

    /**
     * Get a resource instance for a given key.
     *
     * @param  string  $key
     * @return \Laravel\Nova\Resource|null
     */
    public static function resourceInstanceForKey($key)
    {
        if ($resource = static::resourceForKey($key)) {
            return new $resource($resource::newModel());
        }
    }

    /**
     * Get the resource class name for a given key.
     *
     * @param  string  $key
     * @return class-string<\Laravel\Nova\Resource>|null
     */
    public static function resourceForKey($key)
    {
        return static::resourceCollection()->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }

    /**
     * Get a fresh model instance for the resource with the given key.
     *
     * @param  string  $key
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function modelInstanceForKey($key)
    {
        $resource = static::resourceForKey($key);

        return $resource ? $resource::newModel() : null;
    }

    /**
     * Create a new user instance.
     *
     * @param  \Illuminate\Console\Command  $command
     * @return mixed
     */
    public static function createUser($command)
    {
        if (! static::$createUserCallback) {
            static::createUserUsing();
        }

        return call_user_func(
            static::$createUserCallback,
            ...call_user_func(static::$createUserCommandCallback, $command)
        );
    }

    /**
     * Register the callbacks used to create a new user via the CLI.
     *
     * @param  \Closure(\Illuminate\Console\Command):array  $createUserCommandCallback
     * @param  \Closure(string, string, string):\Illuminate\Database\Eloquent\Model  $createUserCallback
     * @return static
     */
    public static function createUserUsing($createUserCommandCallback = null, $createUserCallback = null)
    {
        if (! $createUserCallback) {
            $createUserCallback = $createUserCommandCallback;
            $createUserCommandCallback = null;
        }

        static::$createUserCommandCallback = $createUserCommandCallback ??
            static::defaultCreateUserCommandCallback();

        static::$createUserCallback = $createUserCallback ??
            static::defaultCreateUserCallback();

        return new static();
    }

    /**
     * Get the default callback used for the create user command.
     *
     * @return \Closure(\Illuminate\Console\Command):array
     */
    protected static function defaultCreateUserCommandCallback()
    {
        return function ($command) {
            return [
                $command->ask('Name'),
                $command->ask('Email Address'),
                $command->secret('Password'),
            ];
        };
    }

    /**
     * Get the default callback used for creating new Nova users.
     *
     * @return \Closure(string, string, string):\Illuminate\Database\Eloquent\Model
     */
    protected static function defaultCreateUserCallback()
    {
        return function ($name, $email, $password) {
            $model = Util::userModel();

            return tap((new $model())->forceFill([
                'name' => $name,
                'email' => $email,
                'is_admin' => 1,
                'password' => Hash::make($password),
            ]))->save();
        };
    }

    /**
     * Set the callable that resolves the user's preferred timezone.
     *
     * @param  (callable(\Illuminate\Http\Request):?string)|null  $userTimezoneCallback
     * @return static
     */
    public static function userTimezone($userTimezoneCallback)
    {
        static::$userTimezoneCallback = $userTimezoneCallback;

        return new static();
    }

    /**
     * Resolve the user's preferred timezone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public static function resolveUserTimezone(Request $request)
    {
        if (static::$userTimezoneCallback) {
            return call_user_func(static::$userTimezoneCallback, $request);
        }
    }

    /**
     * Register new tools with Nova.
     *
     * @param  array<int, \Laravel\Nova\Tool>  $tools
     * @return static
     */
    public static function tools(array $tools)
    {
        static::$tools = array_merge(
            static::$tools,
            $tools
        );

        return new static();
    }

    /**
     * Get the tools registered with Nova.
     *
     * @return array<int, \Laravel\Nova\Tool>
     */
    public static function registeredTools()
    {
        return static::$tools;
    }

    /**
     * Boot the available Nova tools.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public static function bootTools(Request $request)
    {
        collect(static::availableTools($request))->each->boot();
    }

    /**
     * Get the tools registered with Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, \Laravel\Nova\Tool>
     */
    public static function availableTools(Request $request)
    {
        if (is_null(static::user($request))) {
            return [];
        }

        return collect(static::$tools)->filter->authorize($request)->all();
    }

    /**
     * Get the dashboards registered with Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, \Laravel\Nova\Dashboard>
     */
    public static function availableDashboards(Request $request)
    {
        return collect(static::$dashboards)->filter->authorize($request)->all();
    }

    /**
     * Register the dashboards.
     *
     * @param  array  $dashboards
     * @return static
     */
    public static function dashboards(array $dashboards)
    {
        static::$dashboards = array_merge(static::$dashboards, $dashboards);

        return new static();
    }

    /**
     * Get the available dashboard cards for the given request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public static function allAvailableDashboardCards(NovaRequest $request)
    {
        return collect(static::$dashboards)
            ->filter
            ->authorize($request)
            ->flatMap(function ($dashboard) {
                return $dashboard->cards();
            })->unique()
            ->filter
            ->authorize($request)
            ->values();
    }

    /**
     * Get the available dashboard for the given request.
     *
     * @param  string  $dashboard
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Dashboard|null
     */
    public static function dashboardForKey($dashboard, NovaRequest $request)
    {
        return collect(static::$dashboards)
            ->first(function ($dash) use ($dashboard, $request) {
                return $dash->uriKey() === $dashboard && $dash->authorize($request);
            });
    }

    /**
     * Get the available dashboard cards for the given request.
     *
     * @param  string  $dashboard
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Illuminate\Support\Collection
     */
    public static function availableDashboardCardsForDashboard($dashboard, NovaRequest $request)
    {
        return with(static::dashboardForKey($dashboard, $request), function ($dashboard) use ($request) {
            if (is_null($dashboard)) {
                return collect();
            }

            return collect($dashboard->cards())->filter->authorize($request)->values();
        });
    }

    /**
     * Get all of the additional scripts that should be registered.
     *
     * @return array<int, \Laravel\Nova\Script>
     */
    public static function allScripts()
    {
        return static::$scripts;
    }

    /**
     * Get all of the available scripts that should be registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, \Laravel\Nova\Script>
     */
    public static function availableScripts(Request $request)
    {
        if (is_null(static::user($request))) {
            return [];
        }

        return static::$scripts;
    }

    /**
     * Get all of the additional stylesheets that should be registered.
     *
     * @return array<int, \Laravel\Nova\Style>
     */
    public static function allStyles()
    {
        return static::$styles;
    }

    /**
     * Get all of the available stylesheets that should be registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, \Laravel\Nova\Style>
     */
    public static function availableStyles(Request $request)
    {
        if (is_null(static::user($request))) {
            return [];
        }

        return static::$styles;
    }

    /**
     * Register the given remote script file with Nova.
     *
     * @param  string  $path
     * @return static
     */
    public static function remoteScript($path)
    {
        return static::script(Script::remote($path), $path);
    }

    /**
     * Register the given script file with Nova.
     *
     * @param  string|\Laravel\Nova\Script  $name
     * @param  string  $path
     * @return static
     */
    public static function script($name, $path)
    {
        static::$scripts[] = new Script($name, $path);

        return new static();
    }

    /**
     * Register the given remote CSS file with Nova.
     *
     * @param  string  $path
     * @return static
     */
    public static function remoteStyle($path)
    {
        return static::style(Style::remote($path), $path);
    }

    /**
     * Register the given CSS file with Nova.
     *
     * @param  string|\Laravel\Nova\Style  $name
     * @param  string  $path
     * @return static
     */
    public static function style($name, $path)
    {
        static::$styles[] = new Style($name, $path);

        return new static();
    }

    /**
     * Register the given translations with Nova.
     *
     * @param  array<string, string>|string  $translations
     * @return static
     */
    public static function translations($translations)
    {
        if (is_string($translations)) {
            if (! is_readable($translations)) {
                return new static();
            }

            $translations = json_decode(file_get_contents($translations), true);
        }

        static::$translations = array_merge(static::$translations, $translations);

        return new static();
    }

    /**
     * Get all of the additional translations that should be loaded.
     *
     * @return array<string, string>
     */
    public static function allTranslations()
    {
        return static::$translations;
    }

    /**
     * Get the JSON variables that should be provided to the global Nova JavaScript object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public static function jsonVariables(Request $request)
    {
        return collect(static::$jsonVariables)->map(function ($variable) use ($request) {
            return is_object($variable) && is_callable($variable)
                ? $variable($request)
                : $variable;
        })->all();
    }

    /**
     * Configure Nova to not register its migrations.
     *
     * @return static
     */
    public static function ignoreMigrations()
    {
        static::$runsMigrations = false;

        return new static();
    }

    /**
     * Humanize the given value into a proper name.
     *
     * @param  string|object  $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        }

        return Str::title(Str::snake($value, ' '));
    }

    /**
     * Register the callback used to set a custom Nova error reporter.
     *
     * @param  (\Closure(\Throwable):void)|(callable(\Throwable):void)|null  $callback
     * @return static
     */
    public static function report($callback)
    {
        static::$reportCallback = $callback;

        return new static();
    }

    /**
     * Provide additional variables to the global Nova JavaScript object.
     *
     * @param  array<string, mixed>  $variables
     * @return static
     */
    public static function provideToScript(array $variables)
    {
        if (empty(static::$jsonVariables)) {
            $userId = Auth::guard(config('nova.guard'))->id() ?? null;

            static::$jsonVariables = [
                'logo' => static::logo(),
                'brandColors' => static::brandColors(),
                'brandColorsCSS' => static::brandColorsCSS(),
                'rtlEnabled' => function () {
                    return static::rtlEnabled();
                },
                'globalSearchEnabled' => function () {
                    return static::globalSearchIsEnabled() && static::hasGloballySearchableResources();
                },
                'notificationCenterEnabled' => function () {
                    return static::$withNotificationCenter;
                },
                'hasGloballySearchableResources' => function () {
                    return static::hasGloballySearchableResources();
                },
                'themeSwitcherEnabled' => function () {
                    return static::$withThemeSwitcher;
                },
                'withAuthentication' => static::$withAuthentication,
                'withPasswordReset' => static::$withPasswordReset,
                'customLoginPath' => config('nova.routes.login', false),
                'customLogoutPath' => config('nova.routes.logout', false),
                'forgotPasswordPath' => config('nova.routes.forgot_password', false),
                'resetPasswordPath' => config('nova.routes.reset_password', false),
                'debounce' => static::$debounce * 1000,
                'initialPath' => static::$initialPath,
                'base' => static::path(),
                'userId' => $userId,
                'mainMenu' => function ($request) use ($userId) {
                    return ! is_null($userId) ? Menu::wrap(self::resolveMainMenu($request)) : [];
                },
                'userMenu' => function ($request) use ($userId) {
                    return ! is_null($userId) ? Menu::wrap(self::resolveUserMenu($request)) : Menu::make();
                },
                'notificationPollingInterval' => static::$notificationPollingInterval * 1000,
                'resources' => function ($request) {
                    return static::resourceInformation($request);
                },
                'footer' => function ($request) {
                    return self::resolveFooter($request);
                },
            ];
        }

        static::$jsonVariables = array_merge(static::$jsonVariables, $variables);

        return new static();
    }

    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return bool
     */
    public static function checkLicenseValidity()
    {
        return Cache::remember('nova_valid_license_key', 3600, function () {
            return rescue(function () {
                return 1;
            }, false);
        });
    }

    /**
     * Check to see if Nova is valid for the configured license key.
     *
     * @return \Illuminate\Http\Client\Response
     */
    public static function checkLicense()
    {
        return Http::post('https://nova.laravel.com', [
            'url' => request()->getHost(),
            'key' => config('nova.license_key', ''),
        ]);
    }

    /**
     * Get the logo that is configured for the Nova admin.
     *
     * @return string|null
     */
    public static function logo()
    {
        $logo = config('nova.brand.logo');

        if (! empty($logo) && file_exists(realpath($logo))) {
            return file_get_contents(realpath($logo));
        }

        return $logo;
    }

    /**
     * Get Nova's content direction.
     *
     * @return bool
     */
    public static function rtlEnabled()
    {
        if (is_callable(static::$rtlCallback)) {
            static::$rtlCallback = value(static::$rtlCallback, app(NovaRequest::class));
        }

        return (bool) static::$rtlCallback;
    }

    /**
     * Enable RTL content direction.
     *
     * @param  (\Closure():bool)|bool  $rtlCallback
     * @return static
     */
    public static function enableRTL($rtlCallback = true)
    {
        static::$rtlCallback = $rtlCallback;

        return new static;
    }

    /**
     * Determine if there are any globally searchable resources.
     *
     * @return bool
     */
    public static function hasGloballySearchableResources()
    {
        return collect(static::globallySearchableResources(app(NovaRequest::class)))->count() > 0;
    }

    /**
     * Determine if global search is enabled.
     *
     * @return bool
     */
    public static function globalSearchIsEnabled(): bool
    {
        return static::$withGlobalSearch;
    }

    /**
     * Get the resources available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, class-string<\Laravel\Nova\Resource>>
     */
    public static function globallySearchableResources(Request $request)
    {
        return static::authorizedResources($request)
            ->searchable()
            ->sortBy(static::sortResourcesWith())
            ->all();
    }

    /**
     * Get the URI path prefix utilized by Nova.
     *
     * @return string
     */
    public static function path()
    {
        return config('nova.path', '/nova');
    }

    /**
     * Resolve the main menu for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\Menu
     */
    public static function resolveMainMenu(Request $request)
    {
        $defaultMenu = static::defaultMainMenu($request);

        if (! is_null(static::$mainMenuCallback)) {
            return call_user_func(static::$mainMenuCallback, $request, $defaultMenu);
        }

        return $defaultMenu;
    }

    /**
     * Resolve the default main menu for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\Menu
     */
    public static function defaultMainMenu(Request $request)
    {
        return Menu::make(with(collect(static::availableTools($request)), function ($tools) use ($request) {
            return $tools->map(function ($tool) use ($request) {
                return $tool->menu($request);
            });
        })->filter()->values()->all());
    }

    /**
     * Resolve the user menu for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\Menu
     */
    public static function resolveUserMenu(Request $request)
    {
        $defaultMenu = static::defaultUserMenu($request);

        if (! is_null(static::$userMenuCallback)) {
            return call_user_func(static::$userMenuCallback, $request, $defaultMenu);
        }

        return $defaultMenu;
    }

    /**
     * Resolve the default user menu for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Nova\Menu\Menu
     */
    public static function defaultUserMenu(Request $request)
    {
        return Menu::make([
            //
        ]);
    }

    /**
     * Get meta data information about all resources for client side consumption.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int, array<string, mixed>>
     */
    public static function resourceInformation(Request $request)
    {
        return static::resourceCollection()->map(function ($resource) use ($request) {
            return array_merge([
                'uriKey' => $resource::uriKey(),
                'label' => $resource::label(),
                'singularLabel' => $resource::singularLabel(),
                'createButtonLabel' => $resource::createButtonLabel(),
                'updateButtonLabel' => $resource::updateButtonLabel(),
                'authorizedToCreate' => $resource::authorizedToCreate($request),
                'searchable' => $resource::searchable(),
                'perPageOptions' => $resource::perPageOptions(),
                'tableStyle' => $resource::tableStyle(),
                'showColumnBorders' => $resource::showColumnBorders(),
                'debounce' => $resource::$debounce * 1000,
                'clickAction' => $resource::$clickAction,
            ], $resource::additionalInformation($request));
        })->values()->all();
    }

    /**
     * Dynamically proxy static method calls.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (! property_exists(get_called_class(), $method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }

        return static::${$method};
    }

    /**
     * Register the callback used to sort Nova resources in the sidebar.
     *
     * @param  \Closure(string):mixed  $callback
     * @return static
     */
    public static function sortResourcesBy($callback)
    {
        static::$sortCallback = $callback;

        return new static();
    }

    /**
     * Return the debounce amount to use when using global search.
     *
     * @param  int  $debounce
     * @return static
     */
    public static function globalSearchDebounce($debounce)
    {
        static::$debounce = $debounce;

        return new static();
    }

    /**
     * Set the main menu for Nova.
     *
     * @param  (\Closure(\Illuminate\Http\Request, \Laravel\Nova\Menu\Menu):\Laravel\Nova\Menu\Menu|array)  $callback
     * @return static
     */
    public static function mainMenu($callback)
    {
        static::$mainMenuCallback = $callback;

        return new static();
    }

    /**
     * Set the initial route path when visiting the base Nova url.
     *
     * @param  string  $path
     * @return static
     */
    public static function initialPath($path)
    {
        static::$initialPath = $path;

        return new static();
    }

    /**
     * Set the main menu for Nova.
     *
     * @param  (\Closure(\Illuminate\Http\Request, \Laravel\Nova\Menu\Menu):\Laravel\Nova\Menu\Menu|array)  $userMenuCallback
     * @return static
     */
    public static function userMenu($userMenuCallback)
    {
        static::$userMenuCallback = $userMenuCallback;

        return new static();
    }

    /**
     * Set the polling interval used for Nova's notifications.
     *
     * @param  int  $seconds
     * @return static
     */
    public static function notificationPollingInterval($seconds)
    {
        static::$notificationPollingInterval = $seconds;

        return new static;
    }

    /**
     * Set the footer text used for Nova.
     *
     * @param  (\Closure(\Illuminate\Http\Request):string)  $footerCallback
     * @return static
     */
    public static function footer($footerCallback)
    {
        static::$footerCallback = $footerCallback;

        return new static;
    }

    /**
     * Resolve the footer used for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public static function resolveFooter(Request $request)
    {
        if (! is_null(static::$footerCallback)) {
            return call_user_func(static::$footerCallback, $request);
        }

        return static::defaultFooter($request);
    }

    /**
     * Resolve the default footer text used for Nova.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public static function defaultFooter(Request $request)
    {
        return Blade::render('
            <p class="text-center"><b>Wainwright Panel</b> · v{!! $version !!}</p>
            <p class="text-center">Crafted by <a class="link-default" href="mailto:ryan.west@online.nl">Ryan West</a> inspired by David G. Wainwright.</p>
        ', [
            'version' => static::version(),
            'year' => date('Y'),
        ]);
    }

    /**
     * Disable global search globally.
     *
     * @return static
     */
    public static function withoutGlobalSearch()
    {
        static::$withGlobalSearch = false;

        return new static;
    }

    /**
     * Disable notification center.
     *
     * @return static
     */
    public static function withoutNotificationCenter()
    {
        static::$withNotificationCenter = false;

        return new static;
    }

    /**
     * Disable light/dark mode theme switching.
     *
     * @return static
     */
    public static function withoutThemeSwitcher()
    {
        static::$withThemeSwitcher = false;

        return new static;
    }

    /**
     * Return Nova's custom brand colors.
     *
     * @return array
     */
    public static function brandColors()
    {
        return collect(config('nova.brand.colors'))->reject(function ($value, $key) {
            return is_null($value);
        })->all();
    }

    /**
     * Return the CSS used to override Nova's brand colors.
     *
     * @return string
     */
    public static function brandColorsCSS()
    {
        return Blade::render('
:root {
@foreach($colors as $key => $value)
    --colors-primary-{{ $key }}: {{ $value }};
@endforeach
}', [
            'colors' => static::brandColors(),
        ]);
    }

    /**
     * Translate the given message.
     *
     * @param  string|null  $key
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string|null
     */
    public static function __($key = null, $replace = [], $locale = null)
    {
        return transform(__($key, $replace, $locale), function ($translation) use ($key) {
            return is_string($translation) ? $translation : $key;
        });
    }
}
