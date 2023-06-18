<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class LocaleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        view()->composer('*', function($view) use ($request) {
            if($request->is('*admin*'))
            {
                App::setLocale('en');
            }
            else if(getsetting('is_localization_enabled'))
            {
                if(Auth::check())
                    App::setLocale(Auth::user()->locale);
                else if(request()->has('locale'))
                    App::setLocale(request()->locale);
            }
        });
    }
}
