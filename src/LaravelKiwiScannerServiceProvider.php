<?php

namespace Ricadesign\LaravelKiwiScanner;

use Illuminate\Support\ServiceProvider;

class LaravelKiwiScannerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/kiwi-scanner.php' => config_path('kiwi-scanner.php'),
        ]);
    }
}
