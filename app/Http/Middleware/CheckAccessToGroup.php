<?php

namespace App\Http\Middleware;

use Closure;
use App\Group;

class CheckAccessToGroup
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
        //if the group doesnt exist, redirect home
        if(!($request->group && 
            (is_string($request->group) && Group::where('slug', $request->group)->count())
            ||
            (!is_string($request->group) && get_class($request->group) == \App\Group::class)
        ))
            return redirect('/home');

        if($request->group instanceof \App\Group)
            $group = $request->group;
        else
            $group = Group::where('slug', $request->group)->first();
        if(!$group)
            return redirect('/home');
        $user = $request->user();
        if($group->users()->where('id', $user->id)->exists() || !$group->join_via_registration_page)
            return $next($request);

        return redirect('/groups/'.$request->group.'/register');
    }
}
