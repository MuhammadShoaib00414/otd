<?php

namespace App\Http\Middleware;

use Closure;

class CheckIsOTDTeam
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
        if($request->user()->is_super_admin)
            return $next($request);
        else
            return redirect()->back();
    }
}
