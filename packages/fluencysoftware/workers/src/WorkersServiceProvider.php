<?php

namespace FluencySoftware\Workers;

use Illuminate\Support\ServiceProvider;

class WorkersServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('FluencySoftware\Workers\WorkersController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
    }
}
