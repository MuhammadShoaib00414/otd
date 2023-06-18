<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfNeedsOnboarding
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
        $user = $request->user();
        if(!$user)
            return redirect('/login');
        if ($user->is_onboarded && $user->groups()->exists())
            return $next($request);

        return redirect('onboarding');
    }
}
