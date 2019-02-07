<?php

namespace Slexx\LaravelActions;

use function GuzzleHttp\Psr7\str;
use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class MakeActionCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * @var null|string
     */
    protected $className = null;

    /**
     * @var null|string
     */
    protected $actionType = null;

    /**
     * @var null|string
     */
    protected $model = null;

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The class name of the action.'],
        ];
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return app_path('Actions') . '/' . ltrim(str_replace('\\', '/', $this->argument('class')), '/') . '.php';
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $name = $this->argument('name');

        $stub = parent::replaceClass($stub, $name);
        $stub = str_replace('DummyNamespace', $this->getNamespace($name), $stub);
        $stub = str_replace('DummyAction', $this->getClassName(), $stub);
        $stub = str_replace('DummyModel', $this->getModel(), $stub);
        $stub = str_replace('dummyModel', lcfirst($this->getModel()), $stub);

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
     * @param string $rootNamespace
     * @return string
     */
    public function getDefaultNameSpace($rootNamespace)
    {
        return $rootNamespace . '\\Actions';
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        $parts = explode('\\', ltrim(str_replace('/', '\\', $this->argument('name')), '\\'));
        return $parts[count($parts) - 1];
    }

    /**
     *
     */
    protected function parseName()
    {
        $className = $this->getClassName();
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
