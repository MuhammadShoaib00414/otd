<?php

namespace App\Providers;

use Laravel\Dusk\Browser;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\ServiceProvider;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //custom macros
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
