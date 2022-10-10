<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nova:upgrade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade Laravel Nova 3 to 4';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        // 1. Prepare Main Dashboard.
        $this->call('nova:dashboard', ['name' => 'Main']);

        // 2. Publish assets
        $this->call('vendor:publish', [
            '--tag' => 'nova-assets',
            '--force' => true,
        ]);

        // 3. Replace nova config file
        if ($this->confirm('Backup existing `nova.php` configuration file?')) {
            $this->backupFiles([
                config_path('nova.php'),
            ]);
        }

        $this->call('vendor:publish', [
            '--tag' => 'nova-config',
            '--force' => true,
        ]);

        $path = $this->laravel['config']->get('nova.path', '/');

        $this->replace("'path' => '/nova',", "'path' => '{$path}',", config_path('nova.php'));

        // 4. Replace nova language files
        if ($this->confirm('Backup existing `en.json` language file?')) {
            $this->backupFiles([
                lang_path('vendor/nova/en.json'),
            ]);
        }

        $this->call('vendor:publish', [
            '--tag' => 'nova-lang',
            '--force' => true,
        ]);

        // 5. Delete Nova 3 layout.blade.php if available.
        $this->backupFiles([
            resource_path('views/vendor/nova/layout.blade.php'),
        ], true);

        // 6. Clear view caches
        $this->call('view:clear');
    }

    /**
     * Create backup to the files.
     *
     * @param  array<int, string>  $files
     * @param  bool  $removeOriginal
     * @return void
     */
    protected function backupFiles(array $files, $removeOriginal = false)
    {
        collect($files)->each(function ($file) use ($removeOriginal) {
            if (File::exists($file)) {
                File::copy($file, "{$file}.backup");

                if ($removeOriginal === true) {
                    File::delete($file);
                }
            }
        });
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string|array  $search
     * @param  string|array  $replace
     * @param  string  $path
     * @return void
     */
    protected function replace($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }
}
