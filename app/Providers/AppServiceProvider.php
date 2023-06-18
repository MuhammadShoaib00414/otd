<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            '*',
            'App\Http\ViewComposers\AccountComposer'
        );
        if(config('app.env') == 'dusk')
            \Debugbar::disable();

        if (Schema::hasTable('settings')) {
            $setting = \App\Setting::where('name', 'from_email_name')->first();
            if ($setting) {
                config(['mail.from' => 
                    [
                        'address' => config('mail.from.address'),
                        'name' => $setting->value,
                    ]
                ]);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->singleton('settings', function () {
            return \Cache::remember('settings', 7200, function () {
                return \App\Setting::all();
            });
        });
        app()->singleton('is_localization_enabled', function () {
            return \Cache::remember('is_localization_enabled', 7200, function () {
                return optional(\DB::table('settings')->where('name', '=', 'is_localization_enabled')->first())->value;
            });
        });
    }
}
