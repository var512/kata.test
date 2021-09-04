<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kata\Interactions\SomeWebService;
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
        $this->app->singleton(WebServiceInterface::class, SomeWebService::class);
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
