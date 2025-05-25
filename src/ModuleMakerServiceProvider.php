<?php

namespace RodGani;

use Illuminate\Support\ServiceProvider;
use RodGani\Console\Commands\MakeModuleCommand;

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
