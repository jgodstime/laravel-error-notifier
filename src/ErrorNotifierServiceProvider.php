<?php

namespace ErrorNotifier\Notify;

use Illuminate\Support\ServiceProvider;

class ErrorNotifierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'notifier');
        $this->mergeConfigFrom(__DIR__.'/config/notifier.php', 'notifier');

        $this->publishes([
            __DIR__.'/config/notifier.php' => config_path('notifier.php'),
            __DIR__.'/resources/views' => resource_path('views/errors'),
        ]);


    }
}
