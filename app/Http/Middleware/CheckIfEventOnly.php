<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckIfEventOnly
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
        if($request->user()->is_event_only && $request->user()->groups()->exists())
        {
            if($request->user()->groups()->whereNull('parent_group_id')->count() == 1)
                return redirect('/spa/#/groups/'.$request->user()->groups()->first()->slug);
        }

        return $next($request);
    }
}
