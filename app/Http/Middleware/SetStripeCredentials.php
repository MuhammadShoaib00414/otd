<?php

namespace App\Http\Middleware;

use Closure;

class SetStripeCredentials
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
        if(!is_stripe_enabled())
            $next($request);

        \Illuminate\Support\Env::getRepository()->set('STRIPE_KEY', get_stripe_credentials('key'));
        \Illuminate\Support\Env::getRepository()->set('STRIPE_SECRET', get_stripe_credentials('secret'));

        return $next($request);
    }
}
