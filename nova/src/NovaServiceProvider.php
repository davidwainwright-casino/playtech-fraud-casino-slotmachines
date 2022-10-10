<?php

namespace Laravel\Nova;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class NovaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        $this->registerResources();
        $this->registerCarbonMacros();
        $this->registerCollectionMacros();
        $this->registerRelationsMacros();
    }


    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__.'/Console/stubs/NovaServiceProvider.stub' => app_path('Providers/NovaServiceProvider.php'),
        ], 'nova-provider');

        $this->publishes([
            __DIR__.'/../config/nova.php' => config_path('nova.php'),
        ], 'nova-config');

        $this->publishes([
            __DIR__.'/../public' => public_path('vendor/nova'),
        ], ['nova-assets', 'laravel-assets']);

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/nova'),
        ], 'nova-lang');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'nova-migrations');
    }

    /**
     * Register the package resources such as routes, templates, etc.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadJsonTranslationsFrom(lang_path('vendor/nova'));
    }

    /**
     * Register the Nova Carbon macros.
     *
     * @return void
     */
    protected function registerCarbonMacros()
    {
        Carbon::mixin(new Macros\FirstDayOfQuarter);
        Carbon::mixin(new Macros\FirstDayOfPreviousQuarter);
        CarbonImmutable::mixin(new Macros\FirstDayOfQuarter);
        CarbonImmutable::mixin(new Macros\FirstDayOfPreviousQuarter);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            Console\ActionCommand::class,
            Console\AssetCommand::class,
            Console\BaseResourceCommand::class,
            Console\CardCommand::class,
            Console\CustomFilterCommand::class,
            Console\DashboardCommand::class,
            Console\FilterCommand::class,
            Console\FieldCommand::class,
            Console\InstallCommand::class,
            Console\LensCommand::class,
            Console\CheckLicenseCommand::class,
            Console\PartitionCommand::class,
            Console\ProgressCommand::class,
            Console\PublishCommand::class,
            Console\ResourceCommand::class,
            Console\ResourceToolCommand::class,
            Console\StubPublishCommand::class,
            Console\TableCommand::class,
            Console\TranslateCommand::class,
            Console\ToolCommand::class,
            Console\TrendCommand::class,
            Console\UserCommand::class,
            Console\UpgradeCommand::class,
            Console\ValueCommand::class,
        ]);
    }

    /**
     * Register Collection macros.
     *
     * @return void
     */
    protected function registerCollectionMacros()
    {
        Collection::macro('isAssoc', function () {
            return Arr::isAssoc($this->toBase()->all());
        });
    }

    /**
     * Register Relations macros.
     *
     * @return void
     */
    protected function registerRelationsMacros()
    {
        BelongsToMany::mixin(new Query\Mixin\BelongsToMany());
    }
}
