<?php

namespace Slexx\LaravelActions;

use Illuminate\Support\ServiceProvider;

class ActionsServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register()
    {

    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeActionCommand::class,
            ]);

            if (class_exists('\\Nwidart\\Modules\\Facades\\Module')) {
                config()->set('modules.paths.generator.actions', ['path' => 'Actions', 'generate' => false]);

                $this->commands([
                    MakeModuleActionCommand::class,
                ]);
            }
        }
    }
}
