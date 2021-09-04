<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kata\Interactions\RealWebService;
use Kata\Interactions\WebServiceInterface;

class SomewebserviceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(WebServiceInterface::class, RealWebService::class);
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
