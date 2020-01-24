<?php

namespace Slexx\LaravelActions;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Commands\GeneratorCommand;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Nwidart\Modules\Support\Config\GenerateConfigReader;

class MakeModuleActionCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class.';

    /**
     * @var null|string
     */
    protected $actionType = null;

    /**
     * @var null|string
     */
    protected $model = null;

    public function getDefaultNamespace(): string
    {
        return $this->laravel['modules']->config('paths.generator.actions.path', 'Actions');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the action class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        $stub = file_get_contents($this->getStub());
        $stub = str_replace('$Namespace$', $this->getClassNamespace($module), $stub);
        $stub = str_replace('$Class$', $this->getClass(), $stub);
        $stub = str_replace('$Model$', $this->getModel(), $stub);
        $stub = str_replace('$model$', lcfirst($this->getModel()), $stub);

        return $stub;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        switch($this->getActionType()) {
            case 'create': return __DIR__ . '/Stubs/CreateAction.stub';
            case 'update': return __DIR__ . '/Stubs/UpdateAction.stub';
            case 'delete': return __DIR__ . '/Stubs/DeleteAction.stub';
            default:       return __DIR__ . '/Stubs/UnknownAction.stub';
        }
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $rulePath = GenerateConfigReader::read('actions');

        return $path . $rulePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     *
     */
    protected function parseName()
    {
        $className = $this->getClass();
        preg_match('/^(Create|Update|Delete)([A-Z][a-z0-9]+)Action$/', $className, $matches);
        if ($matches) {
            $this->actionType = mb_strtolower($matches[1]);
            $this->model = $matches[2];
        }
    }

    /**
     * @return string|null
     */
    public function getActionType()
    {
        if ($this->actionType === null) {
            $this->parseName();
        }

        return $this->actionType;
    }

    /**
     * @return string|null
     */
    public function getModel()
    {
        if ($this->model === null) {
            $this->parseName();
        }

        return $this->model;
    }
}
