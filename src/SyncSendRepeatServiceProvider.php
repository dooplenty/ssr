<?php

namespace Dooplenty\SyncSendRepeat;

use Illuminate\Support\ServiceProvider;

class SyncSendRepeatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'syncsendrepeat');

        $this->publishes(
            [
                __DIR__.'/migrations' => database_path('migrations')
            ],
            'migrations'
        );

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/dooplenty/syncsendrepeat'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__ . '/routes.php';
    }
}
