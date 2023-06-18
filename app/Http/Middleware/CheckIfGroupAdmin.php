<?php

namespace App\Http\Middleware;

use Closure;
use App\Group;

class CheckIfGroupAdmin
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
        $group = Group::where('slug', '=', $request->group)->first();

        if ( ! $group->isUserAdmin($request->user()->id))
            return redirect('/groups/' . $request->group);

        return $next($request);
    }
}
