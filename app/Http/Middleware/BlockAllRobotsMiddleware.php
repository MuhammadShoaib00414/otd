<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\RobotsMiddleware\RobotsMiddleware;
class BlockAllRobotsMiddleware extends RobotsMiddleware
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
        return $next($request);
    }
    protected function shouldIndex(Request $request)
    {
        return 'noindex';
    }
}
