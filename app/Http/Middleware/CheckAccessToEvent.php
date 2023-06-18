<?php

namespace App\Http\Middleware;

use Closure;
use App\Event;
use App\Group;

class CheckAccessToEvent
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
        if($request->user()->is_admin)
            return $next($request);

        $event = false;

        if($request->event instanceof \App\Event)
            $event = $request->event;
        else if(is_string($request->event))
            $event = Event::find($request->event);

        $group = $event ? $event->getGroupFromUser($request->user()->id) : false;
        $userHasRsvp = $event ? $event->eventRsvps()->pluck('user_id')->contains($request->user()->id) : false;

        if(($request->is('events/*') && $userHasRsvp) || $group)
            return $next($request);
        else
        {
            if($event && $userHasRsvp)
                return redirect('/events/' . $request->event);
            else
                return redirect('/home');
        }
    }
}
