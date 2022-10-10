<?php

namespace Laravel\Nova\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class CustomFilterCommand extends ComponentGeneratorCommand
{
    use RenamesStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:custom-filter {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom filter';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->hasValidNameArgument()) {
            return;
        }

        (new Filesystem)->copyDirectory(
            __DIR__.'/filter-stubs',
            $this->componentPath()
        );

        // Filter.js replacements...
        $this->replace('{{ component }}', $this->componentName(), $this->componentPath().'/resources/js/filter.js');

        // Filter.php replacements...
        $this->replace('{{ namespace }}', $this->componentNamespace(), $this->componentPath().'/src/Filter.stub');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/src/Filter.stub');
        $this->replace('{{ component }}', $this->componentName(), $this->componentPath().'/src/Filter.stub');

        (new Filesystem)->move(
            $this->componentPath().'/src/Filter.stub',
            $this->componentPath().'/src/'.$this->componentClass().'.php'
        );

        // FilterServiceProvider.php replacements...
        $this->replace('{{ namespace }}', $this->componentNamespace(), $this->componentPath().'/src/FilterServiceProvider.stub');
        $this->replace('{{ component }}', $this->componentName(), $this->componentPath().'/src/FilterServiceProvider.stub');

        // webpack.mix.js replacements...
        $this->replace('{{ name }}', $this->component(), $this->componentPath().'/webpack.mix.js');

        // Filter composer.json replacements...
        $this->prepareComposerReplacements();

        // Rename the stubs with the proper file extensions...
        $this->renameStubs();

        // Register the filter...
        $this->buildComponent('filter');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/filter.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Nova\Filters';
    }

    /**
     * Get the array of stubs that need PHP file extensions.
     *
     * @return array
     */
    protected function stubsToRename()
    {
        return [
            $this->componentPath().'/src/FilterServiceProvider.stub',
        ];
    }

    /**
     * Get the "title" name of the filter.
     *
     * @return string
     */
    protected function componentTitle()
    {
        return Str::title(str_replace('-', ' ', $this->componentName()));
    }
}
