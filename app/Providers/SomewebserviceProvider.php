<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kata\Interactions\RealSomewebservice;
use Kata\Interactions\SomewebserviceInterface;

class SomewebserviceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SomewebserviceInterface::class, RealSomewebservice::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
