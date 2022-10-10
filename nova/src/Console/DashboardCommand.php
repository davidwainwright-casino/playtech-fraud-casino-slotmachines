<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class DashboardCommand extends GeneratorCommand
{
    use ResolvesStubPath;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:dashboard {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new dashboard.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Dashboard';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $stub = str_replace('uri-key', Str::snake($this->argument('name'), '-'), $stub);

        return str_replace('dashboard-name', ucwords(Str::snake($this->argument('name'), ' ')), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        if ($this->argument('name') === 'Main') {
            return $this->resolveStubPath('/stubs/nova/main-dashboard.stub');
        }

        return $this->resolveStubPath('/stubs/nova/dashboard.stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Nova\Dashboards';
    }
}
