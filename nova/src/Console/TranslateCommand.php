<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:translate
                                {language}
                                {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create translation files for Nova';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $language = $this->argument('language');

        $jsonLanguageFile = lang_path("vendor/nova/{$language}.json");

        if (! File::exists($jsonLanguageFile) || $this->option('force')) {
            File::copy(__DIR__.'/../../resources/lang/en.json', $jsonLanguageFile);
        }
    }
}
