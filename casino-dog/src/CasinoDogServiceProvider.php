<?php

namespace Wainwright\CasinoDog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Wainwright\CasinoDog\Commands\InstallCasinoDog;
use Wainwright\CasinoDog\Commands\MigrateCasinoDog;
use Wainwright\CasinoDog\Commands\AutoConfigCasinoDog;
use Illuminate\Support\ServiceProvider;
use Wainwright\CasinoDog\Commands\AddOperatorAccessKey;

class CasinoDogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('casino-dog')
            ->hasConfigFile('casino-dog')
            ->hasRoutes(['web', 'api', 'games'])
            ->hasViews('wainwright')
            ->hasMigrations(['create_gamerespin_template_table', 'create_games_thumbnails', 'create_bgaming_bonusgames_table', 'modify_users_table', 'create_crawlerdata_table', 'create_game_importer_job', 'create_datalogger_table', 'create_gameslist_table', 'create_metadata_table', 'create_parent_sessions', 'create_rawgameslist_table', 'create_operatoraccess_table'])
            ->hasCommands(AddOperatorAccessKey::class, AutoConfigCasinoDog::class, InstallCasinoDog::class, MigrateCasinoDog::class);

            $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
	    //$kernel->pushMiddleware(\Wainwright\CasinoDog\Middleware\RestrictIpAddressMiddleware::class);

            //Register the proxy
            $this->app->bind('ProxyHelper', function($app) {
                return new ProxyHelper();
            });

    }

}
