<?php

namespace Gouda\LaravelHyperpay;


use Illuminate\Support\ServiceProvider;

class HyperPayServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');


        $this->publishes([
            __DIR__ . '/../config/hyperPay.php' => config_path('HyperPay.php'),
        ], 'HyperPay-package-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/hyperPay.php', 'hyperPay');
    }

}