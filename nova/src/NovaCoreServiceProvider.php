<?php

namespace Laravel\Nova;

use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Logout;
use Illuminate\Container\Container;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Auth\Adapters\SessionImpersonator;
use Laravel\Nova\Contracts\ImpersonatesUsers;
use Laravel\Nova\Contracts\QueryBuilder;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Middleware\ServeNova;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Listeners\BootNova;
use Laravel\Nova\Query\Builder;
use Laravel\Octane\Events\RequestReceived;
use Spatie\Once\Cache;

/**
 * The primary purpose of this service provider is to push the ServeNova
 * middleware onto the middleware stack so we only need to register a
 * minimum number of resources for all other incoming app requests.
 */
class NovaCoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        Nova::booted(BootNova::class);

        if ($this->app->runningInConsole()) {
            $this->app->register(NovaServiceProvider::class);
        }

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/nova.php', 'nova');
        }

        Route::middlewareGroup('nova', config('nova.middleware', []));
        Route::middlewareGroup('nova:api', config('nova.api_middleware', []));

        $this->app->make(HttpKernel::class)
                    ->pushMiddleware(ServeNova::class);

        $this->app->afterResolving(NovaRequest::class, function ($request, $app) {
            if (! $app->bound(NovaRequest::class)) {
                $app->instance(NovaRequest::class, $request);
            }
        });

        $this->registerEvents();
        $this->registerResources();
        $this->registerJsonVariables();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('NOVA_PATH')) {
            define('NOVA_PATH', realpath(__DIR__.'/../'));
        }

        $this->app->singleton(ImpersonatesUsers::class, SessionImpersonator::class);

        $this->app->bind(QueryBuilder::class, function ($app, $parameters) {
            return new Builder(...$parameters);
        });
    }

    /**
     * Register the package events.
     *
     * @return void
     */
    protected function registerEvents()
    {
        tap($this->app['events'], function ($event) {
            $event->listen(Attempting::class, function () {
                app(ImpersonatesUsers::class)->flushImpersonationData(request());
            });

            $event->listen(Logout::class, function () {
                app(ImpersonatesUsers::class)->flushImpersonationData(request());
            });

            $event->listen(RequestReceived::class, function ($event) {
                Nova::flushState();
                // @phpstan-ignore-next-line
                Cache::getInstance()->flush();

                $event->sandbox->forgetInstance(ImpersonatesUsers::class);
            });

            $event->listen(RequestHandled::class, function ($event) {
                Container::getInstance()->forgetInstance(NovaRequest::class);
            });
        });
    }

    /**
     * Register the package resources such as routes, templates, etc.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nova');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'nova');

        if (Nova::runsMigrations()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        });
    }

    /**
     * Get the Nova route group configuration array.
     *
     * @return array{domain: string|null, as: string, prefix: string, middleware: string}
     */
    protected function routeConfiguration()
    {
        return [
            'domain' => config('nova.domain', null),
            'as' => 'nova.api.',
            'prefix' => 'nova-api',
            'middleware' => 'nova:api',
        ];
    }

    /**
     * Register the Nova JSON variables.
     *
     * @return void
     */
    protected function registerJsonVariables()
    {
        Nova::serving(function (ServingNova $event) {
            // Load the default Nova translations.
            Nova::translations(
                lang_path('vendor/nova/'.app()->getLocale().'.json')
            );

            Nova::provideToScript([
                'appName' => Nova::name() ?? config('app.name', 'Laravel Nova'),
                'timezone' => config('app.timezone', 'UTC'),
                'translations' => function () {
                    return Nova::allTranslations();
                },
                'userTimezone' => function ($request) {
                    return Nova::resolveUserTimezone($request);
                },
                'pagination' => config('nova.pagination', 'links'),
                'locale' => config('app.locale', 'en'),
                'algoliaAppId' => config('services.algolia.appId'),
                'algoliaApiKey' => config('services.algolia.apiKey'),
                'version' => Nova::version(),
            ]);
        });
    }
}
