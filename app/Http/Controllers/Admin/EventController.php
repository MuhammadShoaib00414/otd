<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Event;
use Carbon\Carbon;
use App\Exports\RsvpExport;
use Illuminate\Http\Request;
use App\Events\EventCreated;
use Maatwebsite\Excel\Excel;
use App\Http\Controllers\Controller;

class EventController extends Controller
{
    public function index()
    {
        return view('admin.events.index')->with([
            'events' => Event::orderBy('date', 'desc')->withTrashed()->paginate(50),
        ]);
    }

    public function calendar()
    {
        return view('admin.events.calendar')->with([
            'events' => Event::all(),
        ]);
    }

    public function show(Request $request, $id)
    {
        $event = Event::withTrashed()->find($id);
        $users = User::where('users.is_enabled', 1)->whereNotIn('users.id', $event->attending()->pluck('users.id'))->whereNotIn('users.id', $event->notAttending()->pluck('users.id'))->orderBy('users.name', 'desc')->get();

        return view('admin.events.show')->with([
            'event' => $event,
            'users' => $users,
        ]);
    }

    public function edit(Request $request, $id)
    {
        $event = Event::withTrashed()->find($id);

        return view('admin.events.edit')->with([
            'event' => $event,
            'groups' => \App\Group::all(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'file|max:51200',
        ]);

        $startDateTime = Carbon::parse($request->date . ' ' . $request->time)->toDateTimeString();
        $endDateTime = Carbon::parse($request->date . ' ' . $request->input('event_end_time'))->toDateTimeString();
        $timezonedStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime, $request->user()->timezone);
        $timezonedEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime, $request->user()->timezone);

        $event = Event::find($id);

        $event->update([
            'name' => $request->name,
            'date' => $timezonedStart->tz('UTC'),
            'end_date' => $timezonedEnd->tz('UTC'),
            'description' => $request->description,
            'allow_rsvps' => ($request->has('allow_rsvps')) ? 1 : 0,
            'localization' => $request->localization,
            'recur_every' => $request->has('recur_weekly') ? 'week' : '',
            'recur_until' => $request->recurrance_end_date,
        ]);

        if ($request->has('image')) {
            $event->update([
                'image' => $request->file('image')->store('event-images', 's3'),
            ]);
        }

        $groups = collect($request->groups)->merge($request->group);

        $event->groups()->sync($groups);

        $event->listing->groups()->sync($groups);

        return redirect('/admin/events/' . $id);
    }

    public function delete(Request $request, $id)
    {
        $event = Event::withTrashed()->find($id);
        $event->delete();
        $event->notifications()->delete();

        return redirect('/admin/events');
    }

    public function restore(Request $request, $id)
    {
        $event = Event::withTrashed()->find($id);
        $event->restore();
        
        return redirect('/admin/events/' . $id);
    }

    public function create()
    {
        return view('admin.events.create')->with([
            'groups' => \App\Group::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:255',
            'date' => 'required',
            'time' => 'required',
            'image' => 'file|max:51200',
        ]);

        $startDateTime = Carbon::parse($request->date . ' ' . $request->time)->toDateTimeString();
        $endDateTime = Carbon::parse($request->date . ' ' . $request->input('event_end_time'))->toDateTimeString();
        $timezonedStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime, $request->user()->timezone);
        $timezonedEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime, $request->user()->timezone);

        $event = Event::create([
            'name' => $request->name,
            'date' => $timezonedStart->tz('UTC'),
            'end_date' => $timezonedEnd->tz('UTC'),
            'image' => ($request->has('image')) ? $request->file('image')->store('event-images', 's3') : null,
            'description' => $request->description,
            'allow_rsvps' => ($request->has('allow_rsvps')) ? 1 : 0,
            'created_by' => $request->user()->id,
            'group_id' => $request->group,
            'max_participants' => $request->max_participants,
            'localization' => $request->localization,
            'recur_every' => $request->has('recur_weekly') ? 'week' : '',
            'recur_until' => $request->recurrance_end_date,
        ]);
        $post = \App\Post::create([
            'post_type' => get_class($event),
            'post_id' => $event->id,
            'group_id' => $request->group,
        ]);

        $groups = collect($request->groups)->merge($request->group);

        $event->groups()->sync($groups);

        $post->groups()->sync($groups);

        event(new EventCreated($request->user(), $event));

        return redirect("/admin/events/".$event->id);
    }

    public function addUser($eventId, Request $request)
    {
        \App\EventRsvp::updateOrCreate([
            'event_id' => $eventId,
            'user_id' => $request->userId,
        ]);

        return redirect('/admin/events/'.$eventId);
    }

    public function rsvpExport($eventId, Excel $excel)
    {
        $event = Event::find($eventId);
        return $excel->download(new RsvpExport($event), $event->name . '- rsvps.xlsx');
    }
    
}
