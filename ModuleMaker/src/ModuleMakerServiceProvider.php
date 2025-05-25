<?php

namespace Rodgani\ModuleMaker;

use Illuminate\Support\ServiceProvider;
use Rodgani\ModuleMaker\Console\Commands\MakeModuleCommand;

class ModuleMakerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModuleCommand::class,
            ]);
        }
    }
}
