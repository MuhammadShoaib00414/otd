<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class CheckLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
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

        return $next($request);
    }
}
