<?php

namespace App\Http\Controllers\Group;

use App\Post;
use App\Event;
use App\Group;
use App\EventRsvp;
use Carbon\Carbon;
use App\WaitlistedUser;
use App\Traits\ZoomTrait;
use App\Events\RsvpChanged;
use App\Events\EventViewed;
use App\Exports\RsvpExport;
use Illuminate\Http\Request;
use App\Events\EventCreated;
use Maatwebsite\Excel\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    use ZoomTrait;

    const MEETING_TYPE_INSTANT = 1;
    const MEETING_TYPE_SCHEDULE = 2;
    const MEETING_TYPE_RECURRING = 3;
    const MEETING_TYPE_FIXED_RECURRING_FIXED = 8;

    public function __construct()
    {
        $this->middleware('group.event')->only('show');
        $this->middleware('auth');
        $this->middleware('group')->except(['rsvp']);
    }
    
    public function index(Request $request, $slug) {
        $group = Group::where('slug', '=', $slug)->first();

        $request->user()->logs()->create([
            'action' => 'viewed calendar',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
        ]);

        return view('groups.events.index')->with([
            'group' => $group,
        ]);
    }

    public function create(Request $request, $slug)
    {
        $group = Group::where('slug', '=', $slug)->first();

        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_events)
            return redirect('/groups/'.$slug);

        return view('groups.events.create')->with([
            'group' => $group,
            'groups' => $request->user()->visible_platform_groups,
        ]);
    }

    public function store(Request $request, $slug)
    {
        $validate = $request->validate([
            'name' => 'required|max:255',
            'date' => 'required',
            'time' => 'required',
            'max_participants' => 'nullable|integer',
            'image' => 'file|max:51200',
        ]);

        $group = Group::where('slug', '=', $slug)->first();

        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_events)
            return redirect('/groups/'.$slug);

        if($request->has('links')) {
            $custom_menu = [];
            foreach($request->links as $link) {
                if(isset($link['title']) && isset($link['url']))
                    $custom_menu[] = $link;
            }
        }

        $startDateTime = Carbon::parse($request->input('date') . ' ' . $request->input('time'))->toDateTimeString();
        $endDate = $request->end_date ? $request->end_date : $request->input('date');
        $endDateTime = Carbon::parse($endDate . ' ' . $request->input('event_end_time'))->toDateTimeString();
        $timezonedStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime, $request->user()->timezone);
        $timezonedEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime, $request->user()->timezone);

        // check that the endtime is AFTER the start time
        if (Carbon::parse($endDateTime)->lt(Carbon::parse($startDateTime)))
            return back()->withErrors(['error' => 'End time must be after start time.']);

        if($request->has('image'))
        {
            $path = $request->file('image')->store('event-images', 's3');
            # optimizeImage(public_path() . '/uploads/' . $path, 1000);
        }
        else
            $path = '';
        
        $event = Event::create([
            'name' => $request->name,
            'date' => $timezonedStart->tz('UTC'),
            'end_date' => $timezonedEnd->tz('UTC'),
            'image' => $path,
            'description' => $request->description,
            'allow_rsvps' => ($request->has('allow_rsvps')) ? 1 : 0,
            'created_by' => $request->user()->id,
            'group_id' => $group->id,
            'max_participants' => $request->max_participants,
            'custom_menu' => $request->has('links') ? $custom_menu : null,
            'localization' => $request->has('localization') ? $request->localization : null,
            'recur_every' => $request->has('recur_weekly') ? 'week' : '',
            'recur_until' => $request->recurrance_end_date ? Carbon::parse($request->recurrance_end_date)->endOfDay() : null,
        ]);

        $groups = collect($request->groups)->merge($group->id);

        $event->groups()->sync($groups);

        $post = Post::create([
            'post_type' => get_class($event),
            'post_id' => $event->id,
            'group_id' => $group->id,
            'is_enabled' => $request->has('post_to_group_feed'),
        ]);

        $post->groups()->sync($groups);

        $group->resetOrderedPosts();

        if($request->has('users'))
        {
            foreach($request->users as $userId)
            {
                $event->eventRsvps()->create([
                    'user_id' => $userId,
                    'response' => null,
                ]);
            }
        }
        if(!$event->has_happened)
            event(new EventCreated($request->user(), $event));

        if($request->has('enable_zoom'))
        {
            $zoom_meeting_data = $this->parseZoomLink($request->zoom_meeting_link);
            if(!$zoom_meeting_data)
                return redirect()->back()->withErrors('msg', 'This zoom invite link is invalid.');

            $event->update($zoom_meeting_data);
        }

        return redirect("/groups/{$group->slug}/events/{$event->id}");
    }

    public function parseZoomLink($link)
    {
        if(!array_key_exists(4, explode('/', $link)))
            return false;

        $exploded_link = explode('/', $link);
        $relevant_information = $exploded_link[array_key_last($exploded_link)];

        $data = explode('?pwd=', $relevant_information);

        return [
            'zoom_meeting_id' => $data[0],
            'zoom_meeting_password' => array_key_exists(1, $data) ? $data[1] : '',
            'zoom_invite_link' => $link,
        ];
    }

    public function show(Request $request, $slug, $event)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $event = Event::find($event);
        
        if ($event == null)
            return "Whoops! This event has been deleted.";

        $userRsvp = $event->eventRsvps()->where('user_id', '=', $request->user()->id)->first();
        $userWaitlisted = $event->waitlist()->where('user_id', '=', $request->user()->id)->first();
        event(new EventViewed($request->user(), $event));

        $request->user()->logs()->create([
            'action' => 'viewed event',
            'message' => '<a href="/groups/'.$group->id.'/events/'.$event->id.'">'.$event->name.'</a>',
            'related_model_type' => 'App\Group',
            'related_model_id' => $group->id,
            'secondary_related_model_type' => 'App\Event',
            'secondary_related_model_id' => $event->id,
        ]);

        return view('groups.events.show')->with([
            'group' => $group,
            'event' => $event,
            'userRsvp' => $userRsvp,
            'userWaitlisted' => $userWaitlisted,
        ]);
    }

    public function rsvp(Request $request, $slug, $event)
    {
        $event = Event::find($event);
        if(!$event->has_max_participants || $request->input('rsvp') == 'no')
        {
            $rsvp = EventRsvp::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => $request->user()->id],
                ['response' => $request->input('rsvp')]
            );
            event(new RsvpChanged($request->user(), $rsvp));

            if($event->waitlist->count() && !$event->has_max_participants)
                $event->popWaitlist();

            
            $event->syncMessageThreads();
        }
        else if($event->has_max_participants && !$event->waitlist_users->where('id', $request->user()->id)->count())
        {
            $event->waitlist()->create([
                'user_id' => $request->user()->id,
            ]);
        }

        $event->touch();
        $request->user()->touch();

        return redirect("/groups/{$slug}/events/{$event->id}");
    }

    public function waitlist(Request $request, $slug, $eventId)
    {
        if($request->waitlist == 'leave')
        {
            WaitlistedUser::where('user_id', $request->user()->id)->delete();
        }
        else
        {
            WaitlistedUser::create([
                'event_id' => $eventId,
                'user_id' => $request->user()->id,
            ]);
        }

        Event::find($eventId)->syncMessageThreads();

        return redirect('/groups/' . $slug . '/events/' . $eventId);
    }

    public function edit(Request $request, $slug, $event)
    {
        $group = Group::where('slug', '=', $slug)->first();
        $event = Event::find($event);
        $groups = Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get();

        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_events)
            return redirect('/groups/'.$slug);

        return view('groups.events.edit')->with([
            'group' => $group,
            'event' => $event,
            'groups' => $groups,
        ]);
    }

    public function update(Request $request, $slug, $event)
    {
        $request->validate([
            'image' => 'file|max:51200',
        ]);
        $event = Event::find($event);
      
        if(isset($request->update_posted_date)){
             Post::where('post_id', $event->id)->update(array('post_at' => Carbon::now()->toDateTimeString()));
        }
       
        $old_max_participants = $event->max_participants;

        $imageArray = [];
        if ($request->has('image'))
            $imageArray = ['image' => $request->file('image')->store('event-images')];

        $startDateTime = Carbon::parse($request->input('event_date') . ' ' . $request->input('event_time'))->toDateTimeString();
        $endDate = $request->has('end_date') ? $request->end_date : $request->event_date;
        $endDateTime = Carbon::parse($endDate . ' ' . $request->input('event_end_time'))->toDateTimeString();
        $timezonedStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime, $request->user()->timezone);
        $timezonedEnd = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $endDateTime, $request->user()->timezone);

        // check that the endtime is AFTER the start time
        if (Carbon::parse($endDateTime)->lt(Carbon::parse($startDateTime)))
            return back()->withErrors(['error' => 'End time must be after start time.']);

        if($request->has('links'))
        {
            $custom_menu = [];
            foreach($request->links as $link)
            {
                if(isset($link['title']) && isset($link['url']))
                {
                    $custom_menu[] = $link;
                }
            }
        }

        if($request->post_to_group_feed != $event->listing->is_enabled)
        {
            $event->listing()->update([
                'is_enabled' => $request->has('post_to_group_feed'),
            ]);
        }

        $event->update(array_merge([
            'name' => $request->name,
            'date' => $timezonedStart->tz('UTC')->toDateTimeString(),
            'end_date' => $timezonedEnd->tz('UTC')->toDateTimeString(),
            'description' => $request->description,
            'allow_rsvps' => ($request->has('allow_rsvps')) ? 1 : 0,
            'max_participants' => $request->max_participants,
            'should_display_live_now' => $request->should_display_live_now ? 1 : 0,
            'should_display_live' => $request->should_display_live ? 1 : 0,
            'custom_menu' => $request->has('links') ? $custom_menu : null,
            'localization' => $request->localization,
            'recur_every' => $request->has('recur_weekly') ? 'week' : '',
            'recur_until' => $request->recurrance_end_date ? Carbon::parse($request->recurrance_end_date)->endOfDay() : null,
        ],$imageArray));

        $event->save();

        $new_max_participants = $request->max_participants;

        if($new_max_participants > $old_max_participants && $event->waitlist()->count())
            $event->bulkPopWaitlist($new_max_participants - $old_max_participants);

      //  $groups = collect($request->groups)->merge($event->group->id);
       // $event->groups()->sync($groups);
       // $post = Post::where('post_type', get_class($event))->where('post_id', $event->id)->first()->groups()->sync($groups);

        if($request->has('enable_zoom') && $request->zoom_meeting_link != $event->zoom_invite_link)
        {
            $zoom_meeting_data = $this->parseZoomLink($request->zoom_meeting_link);
            if(!$zoom_meeting_data)
                return redirect()->back()->withErrors('msg', 'This zoom invite link is invalid.');

            $event->update($zoom_meeting_data);
        }
        else if(!$request->has('enable_zoom') && $event->zoom_invite_link)
            $event->update(['zoom_meeting_id' => null, 'zoom_meeting_password' => null, 'zoom_invite_link' => null]);

        return redirect("/groups/{$slug}/events/{$event->id}");
    }

    public function delete(Request $request, $slug, $event)
    {
        $event = Event::find($event);
        if(!$event)
            return redirect("/groups/{$slug}/calendar");
        
        if($event->group && $event->group->slug == $slug)
        {
            $event->notifications()->delete();
            $event->delete();
        }
        else
        {
            $event->groups()->detach(Group::slug($slug));
        }

        return redirect("/groups/{$slug}/calendar");
    }

    public function cancel($slug, Event $event, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        
        if(!$group->isUserAdmin($request->user()->id) && !$request->user()->is_admin && !$group->can_users_post_events)
            return redirect('/groups/'.$slug);

        if($request->is_cancelled == 1)
        {
            $event->is_cancelled = $request->is_cancelled;
            $event->cancelled_reason = $request->cancelled_reason;

            $usersToEmail = $event->waitlist_users;
            $usersToEmail = $usersToEmail->concat($event->attending);
            
            event(new \App\Events\EventCancelled($usersToEmail, $event));
        }
        else
        {
            $event->is_cancelled = $request->is_cancelled;
            $event->cancelled_reason = null;
        }
        
        $event->save();

        return redirect('/groups/'.$slug.'/events/'.$event->id);
    }

    public function rsvpExport($slug, $eventId, Excel $excel)
    {
        $event = Event::find($eventId);
        return $excel->download(new RsvpExport($event), $event->name . '- rsvps.xlsx');
    }

    public function createZoomMeeting($data, $event)
    {   
        $zoom_user_id = $event->group->zoom_user_id;
        $path = 'users/'.$zoom_user_id.'/meetings';
        $response = $this->zoomPost($path, [
            'topic' => $event->title,
            'type' => 2, // scheduled meeting type
            'start_time' => $this->toZoomTimeFormat($event->date->tz('UTC')->toDateTimeString()),
            'duration' => 30,
            'agenda' => $event->description,
            'passcode' => '1234',
            'settings' => [
                'host_video' => true,
                'participant_video' => false,
                'waiting_room' => true,
                'join_before_host' => true,
                'participant_video' => true,
                'show_share_button' => true,
            ]
        ]);

        $response = json_decode($response->body(), true);

        $event->update([
            'zoom_meeting_id' => $response['id'],
            'zoom_meeting_password' => $response['password'],
        ]);
    }

    

}
