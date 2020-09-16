<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\ServiceProvider;

class LaravelKiwiScannerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->make('Ricadesign\LaravelKiwiScanner\CouponController');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/path/to/config/courier.php' => config_path('courier.php'),
        ]);
        // $this->loadRoutesFrom(__DIR__.'/routes.php');
        // $this->publishes([
        //     __DIR__.'/migrations' => database_path('migrations'),
        // ]);
    }
}
