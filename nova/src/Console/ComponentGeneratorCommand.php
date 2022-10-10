<?php

namespace Laravel\Nova\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Nova\Console\Concerns\AcceptsNameAndVendor;
use Symfony\Component\Process\Process;

abstract class ComponentGeneratorCommand extends Command
{
    use AcceptsNameAndVendor, ResolvesStubPath;

    /**
     * Prepare composer replacements.
     *
     * @return void
     */
    protected function prepareComposerReplacements()
    {
        $composerJson = $this->componentPath().'/composer.json';

        $this->replace('{{ name }}', $this->component(), $composerJson);
        $this->replace('{{ escapedNamespace }}', $this->escapedComponentNamespace(), $composerJson);
    }

    /**
     * Register and build the component.
     *
     * @param  string  $componentType
     * @param  bool  $interactsWithComposer
     * @param  bool  $interactsWithNpm
     * @return void
     */
    protected function buildComponent($componentType, $interactsWithComposer = true, $interactsWithNpm = true)
    {
        if ($interactsWithComposer === true) {
            $this->addRepositoryToRootComposer();
            $this->addRequireToRootComposer();

            if ($this->confirm('Would you like to update your Composer packages?', true)) {
                $this->composerUpdate();

                $this->output->newLine();
            }
        }

        if ($interactsWithNpm === true) {
            if (file_exists(base_path('package.json'))) {
                $this->addScriptsToRootNpmPackage();
            } else {
                $this->warn('Please create a package.json to the root of your project.');
            }

            if ($this->confirm("Would you like to install the {$componentType}'s NPM dependencies?", true)) {
                $this->installNpmDependencies();

                $this->output->newLine();
            }

            if ($this->confirm("Would you like to compile the {$componentType}'s assets?", true)) {
                $this->compileAssets();

                $this->output->newLine();
            }
        }
    }

    /**
     * Run the given command as a process.
     *
     * @param  string  $command
     * @param  string  $path
     * @return void
     */
    protected function executeCommand($command, $path)
    {
        $process = (Process::fromShellCommandline($command, $path))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }

    /**
     * Update the project's composer dependencies.
     *
     * @return void
     */
    protected function composerUpdate()
    {
        $this->executeCommand('composer update', getcwd());
    }

    /**
     * Add a package entry for the component to the application's composer.json file.
     *
     * @return void
     */
    protected function addRequireToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['require'][$this->component()] = '*';

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * Add a path repository for the component to the application's composer.json file.
     *
     * @return void
     */
    protected function addRepositoryToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['repositories'][] = [
            'type' => 'path',
            'url' => './'.$this->relativeComponentPath(),
        ];

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a path repository for the component to the application's composer.json file.
     *
     * @return void
     */
    protected function addScriptsToRootNpmPackage()
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true);

        $package['scripts']['build-'.$this->componentName()] = 'cd '.$this->relativeComponentPath().' && npm run dev';
        $package['scripts']['build-'.$this->componentName().'-prod'] = 'cd '.$this->relativeComponentPath().' && npm run prod';

        file_put_contents(
            base_path('package.json'),
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Install the component's NPM dependencies.
     *
     * @return void
     */
    protected function installNpmDependencies()
    {
        $this->executeCommand('npm set progress=false && npm install', $this->componentPath());
    }

    /**
     * Install the Nova's NPM dependencies.
     *
     * @return void
     */
    protected function installNovaNpmDependencies()
    {
        $this->executeCommand('npm set progress=false && npm ci', realpath(__DIR__.'/../../'));
    }

    /**
     * Compile the component's assets.
     *
     * @return void
     */
    protected function compileAssets()
    {
        $this->executeCommand('npm run dev', $this->componentPath());
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

    /**
     * Get the path to the component.
     *
     * @return string
     */
    protected function componentPath()
    {
        return base_path('nova-components/'.$this->componentClass());
    }

    /**
     * Get the relative path to the component.
     *
     * @return string
     */
    protected function relativeComponentPath()
    {
        return 'nova-components/'.$this->componentClass();
    }

    /**
     * Get the component's namespace.
     *
     * @return string
     */
    protected function componentNamespace()
    {
        return Str::studly($this->componentVendor()).'\\'.$this->componentClass();
    }

    /**
     * Get the component's escaped namespace.
     *
     * @return string
     */
    protected function escapedComponentNamespace()
    {
        return str_replace('\\', '\\\\', $this->componentNamespace());
    }

    /**
     * Get the component's class name.
     *
     * @return string
     */
    protected function componentClass()
    {
        return Str::studly($this->componentName());
    }

    /**
     * Get the component's vendor.
     *
     * @return string
     */
    protected function componentVendor()
    {
        return explode('/', $this->component())[0];
    }

    /**
     * Get the component's base name.
     *
     * @return string
     */
    protected function componentName()
    {
        return explode('/', $this->component())[1];
    }

    /**
     * Get the component's name.
     *
     * @return string
     */
    protected function component()
    {
        return $this->argument('name');
    }
}
