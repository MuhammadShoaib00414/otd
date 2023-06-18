<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->check() && (auth()->user()->is_enabled == 0)){
                Auth::logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Your Account is disabled, please contact Admin.');

        }

        return $next($request);
    }
}
