<?php

namespace App\Http\Middleware;

use Closure;

class checkDebugbar
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
        if(!class_exists('\Debugbar'))
            return $next($request);
        if(!$request->user() || !env('ENABLE_DEBUGBAR'))
            \Debugbar::disable();
        elseif($request->user()->email == 'davis@ipx.org' || $request->user()->email == 'cm@ipx.org')
            \Debugbar::enable();
        else
            \Debugbar::disable();

        return $next($request);
    }
}
