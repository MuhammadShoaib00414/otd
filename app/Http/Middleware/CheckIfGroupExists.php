<?php

namespace App\Http\Middleware;

use Closure;
use App\Group;

class CheckIfGroupExists
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
        if($request->group && 
            (is_string($request->group) && Group::where('slug', $request->group)->count())
            ||
            (!is_string($request->group) && get_class($request->group) == \App\Group::class)
        )
            return $next($request);
        
        return redirect('/home');
    }
}
