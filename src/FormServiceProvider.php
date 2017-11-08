<?php

namespace Mods\Form;

use Mods\Form\ErrorStore\IlluminateErrorStore;
use Mods\Form\OldInput\IlluminateOldInputProvider;
use Illuminate\Support\ServiceProvider;
use Mods\Form\ErrorStore\ErrorStoreInterface;
use Mods\Form\OldInput\OldInputInterface;

class FormServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        $this->registerErrorStore();
        $this->registerOldInput();
    }

    protected function registerErrorStore()
    {
        $this->app->singleton(ErrorStoreInterface::class, function ($app) {
            return new IlluminateErrorStore($app['session.store']);
        });
    }

    protected function registerOldInput()
    {
        $this->app->singleton(OldInputInterface::class, function ($app) {
            return new IlluminateOldInputProvider($app['session.store']);
        });
    }
}
