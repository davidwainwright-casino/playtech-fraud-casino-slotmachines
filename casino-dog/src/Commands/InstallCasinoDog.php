<?php

namespace Wainwright\CasinoDog\Commands;

use Illuminate\Support\Facades\Http;
use Wainwright\CasinoDog\Models\Gameslist;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;
use Symfony\Component\Process\Process;
use Wainwright\CasinoDog\Commands\InstallNovaPanel;

class InstallCasinoDog extends Command
{
    use InstallNovaPanel;

    protected $signature = 'casino-dog:install {panel=none : The stack that should be installed (none, filament, nova)}
                                               {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    public $description = 'Install casino-dog panel.';

    public function handle()
    {
        if($this->confirm('Do you want to install panel stubs?')) {
            $this->installNovaStubs();
        }  else {
            $this->info('.. Skipped installing panel stubs');
        }
        if($this->confirm('Do you want to run database migrations?')) {
            \Artisan::call('vendor:publish --tag="casino-dog-migrations"');
            $this->info('> Running..  "vendor:publish --tag="casino-dog-migrations"');
            \Artisan::call('migrate');
            $this->info('> Running..  "artisan migrate"');
        }  else {
            $this->info('.. Skipped database migrations');
        }

        /* Publish config file*/
        if($this->confirm('Do you want to publish config?')) {
            \Artisan::call('vendor:publish --tag="casino-dog-config"');
            $this->info('> Running..  "vendor:publish --tag="casino-dog-config"');
            $this->info('> Config published in config/casino-dog.php');
        }  else {
            $this->info('.. Skipped publishing config');
        }

        if($this->confirm('Do you want to set API limit in RouteServiceProvider.php to 5000?')) {
            $this->replaceInBetweenInFile("perMinute\(", "\)", '5000', base_path('app/Providers/RouteServiceProvider.php'));
            $this->replaceInFile('$request->ip()', '\Wainwright\CasinoDog\CasinoDog::static_getIp($request)', base_path('app/Providers/RouteServiceProvider.php'));

            $this->info('> Running..  "api limit"');
        }  else {
            $this->info('.. Skipped database migrations');
        }

	    if($this->confirm('Do you want to install Ably?')) {
            $this->requireComposerPackages('ably/ably-php-laravel:^1.0');
            if($this->confirm('Do you want to publish ably config?')) {
            \Artisan::call('vendor:publish --tag="Ably\Laravel\AblyServiceProvider"');
            }
            $this->info('Seems all went fine. You need to now manually add Ably\'s serviceprovider in config/app.php by adding to provider array: ');
            $this->info('"Ably\Laravel\AblyServiceProvider::class,"');       
            $this->info('Without this your application will error as a whole when trying to send socketted messages.');
        }
        if($this->confirm('Do you want to set new Ably apikey?')) {
            $current_key = config('ably.key');
            $this->info('> Current ablykey: '.$current_key);
            $new_key = $this->ask('What is your Ably api key?');
            $this->replaceInFile($current_key, $new_key, base_path('config/ably.php'));
         }

	    if($this->confirm('Do you want to import DB list?')) {
            $http = Http::get('https://ignitebets.com/gameslist.json');
            $http = json_decode($http, true);
            foreach($http as $game) {
            Gameslist::insert($game);
	    }

        
	}
        return self::SUCCESS;
    }
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
    public function replaceInBetweenInFile($a, $b, $replace, $path)
    {
        $file_get_contents = file_get_contents($path);
        $in_between = $this->in_between($a, $b, $file_get_contents);
        if($in_between) {
            $search_string = stripcslashes($a.$in_between.$b);
            $replace_string = stripcslashes($a.$replace.$b);
            file_put_contents($path, str_replace($search_string, $replace_string, file_get_contents($path)));
            return self::SUCCESS;
        }
        return self::SUCCESS;
    }

    public function in_between($a, $b, $data)
    {
        preg_match('/'.$a.'(.*?)'.$b.'/s', $data, $match);
        if(!isset($match[1])) {
            return false;
        }
        return $match[1];
    }

    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = ['php', $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }

}
