<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('group.event')->only('show');
    }

    public function index(Request $request)
    {
        $user = $request->user();

        return view('calendar.index')->with([
            'events' => $user->dashboard_events,
            'notifiedEvents' => $user->events_with_notifications,
        ]);
    }

    public function show(Event $event, Request $request)
    {
        return view('calendar.show')->with([
            'event' => $event,
            'userRsvp' => $event->eventRsvps()->where('user_id', '=', $request->user()->id)->first(),
            'userWaitlisted' => $event->waitlist()->where('user_id', '=', $request->user()->id)->first(),
        ]);
    }
}
