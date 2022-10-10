<?php

namespace Laravel\Nova\Console;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ToolCommand extends ComponentGeneratorCommand
{
    use RenamesStubs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:tool {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tool';

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

        $noInteraction = $this->option('no-interaction');

        (new Filesystem)->copyDirectory(
            __DIR__.'/tool-stubs',
            $this->componentPath()
        );

        // Route replacements...
        $this->replace(['{{ component }}', '{{ name }}'], $this->componentName(), $this->componentPath().'/routes/api.stub');
        $this->replace(['{{ component }}', '{{ name }}'], $this->componentName(), $this->componentPath().'/routes/inertia.stub');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/routes/inertia.stub');

        // Tool.js replacements...
        $this->replace(['{{ component }}', '{{ name }}'], $this->componentName(), $this->componentPath().'/resources/js/tool.js');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/resources/js/tool.js');

        // Tool.vue replacements...
        $this->replace('{{ title }}', $this->componentTitle(), $this->componentPath().'/resources/js/pages/Tool.vue');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/resources/js/pages/Tool.vue');

        // Tool.php replacements...
        $this->replace('{{ namespace }}', $this->componentNamespace(), $this->componentPath().'/src/Tool.stub');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/src/Tool.stub');
        $this->replace('{{ title }}', $this->componentTitle(), $this->componentPath().'/src/Tool.stub');
        $this->replace(['{{ component }}', '{{ name }}'], $this->componentName(), $this->componentPath().'/src/Tool.stub');

        (new Filesystem)->move(
            $this->componentPath().'/src/Tool.stub',
            $this->componentPath().'/src/'.$this->componentClass().'.php'
        );

        // ToolServiceProvider.php replacements...
        $this->replace('{{ namespace }}', $this->componentNamespace(), $this->componentPath().'/src/ToolServiceProvider.stub');
        $this->replace(['{{ component }}', '{{ name }}'], $this->componentName(), $this->componentPath().'/src/ToolServiceProvider.stub');

        // webpack.mix.js replacements...
        $this->replace('{{ name }}', $this->component(), $this->componentPath().'/webpack.mix.js');

        // Authorize.php replacements...
        $this->replace('{{ namespace }}', $this->componentNamespace(), $this->componentPath().'/src/Http/Middleware/Authorize.stub');
        $this->replace('{{ class }}', $this->componentClass(), $this->componentPath().'/src/Http/Middleware/Authorize.stub');

        // Tool composer.json replacements...
        $this->prepareComposerReplacements();

        // Rename the stubs with the proper file extensions...
        $this->renameStubs();

        // Register the tool...
        $this->buildComponent('tool');
    }

    /**
     * Get the array of stubs that need PHP file extensions.
     *
     * @return array
     */
    protected function stubsToRename()
    {
        return [
            $this->componentPath().'/src/ToolServiceProvider.stub',
            $this->componentPath().'/src/Http/Middleware/Authorize.stub',
            $this->componentPath().'/routes/api.stub',
            $this->componentPath().'/routes/inertia.stub',
        ];
    }

    /**
     * Get the "title" name of the tool.
     *
     * @return string
     */
    protected function componentTitle()
    {
        return Str::title(str_replace('-', ' ', $this->componentName()));
    }
}
