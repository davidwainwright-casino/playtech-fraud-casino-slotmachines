<?php

namespace Wainwright\CasinoDog\Commands;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

trait InstallFilamentPanel
{
    /**
     * Install the filamentphp.com panel.
     *
     * @return void
     */
    protected function installFilament()
    {
        // Install Filament...
        $this->requireComposerPackages('filament/filament:^2.0', 'jeffgreco13/filament-breezy:*');

        // Publish Filament config & Database migrations
        \Artisan::call('vendor:publish --tag="filament-config"');
        \Artisan::call('vendor:publish --tag="filament-migrations"');
        $this->line('');
        $this->components->info('Published Filament base config & db migrations');

        //Publish Filament Breezy migrations
        \Artisan::call('vendor:publish --tag="filament-breezy-config"');
        \Artisan::call('vendor:publish --tag="filament-breezy-migrations"');
        \Artisan::call('migrate');
        $this->line('');
        $this->components->info('Published Filament Breezy (auth scaffold) config & db migrations');

        // Publish Casino Dog migrations
        \Artisan::call('vendor:publish --tag="casino-dog-migrations"');
        $this->line('');
        $this->components->info('Published Casino Dog db migrations');

        $this->components->info('Seems install went OK, if this is a fresh install go to your main web dir and run:');
        $this->line('');
        $this->components->info('rm -r app/Filament');
        $this->line('');
        $this->components->info('ln -s wainwright/src/Filament app/Filament');

    }
}
