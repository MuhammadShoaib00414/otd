<?php

namespace App\Http\Controllers\Admin;

use Mail;
use App\Log;

use App\User;
use App\Group;
use Carbon\Carbon;
use App\Invitation;
use App\Mail\InviteUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;

class InvitesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if($request->has('sort'))
            $query = Invitation::orderBy('email', 'asc');
        else
            $query = Invitation::orderBy('sent_at', 'desc');

        if ($request->has('show')) {
            if ($request->show == 'accepted')
                $query = $query->whereNotNull('accepted_at');
            if ($request->show == 'invited')
                $query = $query->whereNull('accepted_at');
        }

        if($request->has('q') && $request->input('q') != '')
            $query = $query->where('email', 'like', '%' . $request->input('q') . '%');

        $invitations = $query->get();


        return view('admin.users.invites.index')->with([
            'invitations' => $invitations,
            'groups' => Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.users.invites.create')->with([
            'existingInvites' => collect(),
            'existingUsers' => collect(),
            'invalidEmails' => collect(),
            'sentTo' => collect(),
            'groups' => Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
        ]);
    }

    public function sendInvite(Request $request)
    {

        $emails = array_unique(explode("\r\n", $request->input('emails')));

        $existingInvites = collect();
        $existingUsers = collect();
        $invalidEmails = collect();
        $sentTo = collect();

        foreach ($emails as $email) {
            $email = preg_replace('/\s+/', '', $email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              continue;
            }
            if (User::where('email', '=', $email)->count())
                $existingUsers[] = $email;
            else if (Invitation::where('email', '=', $email)->count())
                $existingInvites[] = $email;
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == '')
                $invalidEmails[] = $email;
            else {
                
                $invitation = Invitation::create([
                    'email' => $email,
                    'custom_message' => $request->input('custom_message'),
                    'sent_at' => Carbon::now(),
                    'hash' => Str::random(7),
                    'add_to_groups' => $request->has('groups') ? $request->input('groups') : null,
                    'is_event_only' => $request->is_event_only,
                    'expires_at' => $request->has('event_only_expires_at') ? Carbon::parse($request->event_only_expires_at)->toDateTimeString() : null,
                    'groups_admin_of' => $request->has('groupsAdminOf') ? json_encode($request->input('groupsAdminOf')) : null,
                    'locale' => $request->has('locale') ? $request->locale : null,
                ]);
             
                $logs = Log::create([
                    'action'             => 'send invitation',
                    'user_id' => Auth::user()->id,
                    'message' => $invitation->hash,
                    'related_model_type' => 'App\User',
                    'related_model_id' => Auth::user()->id,
                    'track_info' =>  UserTrackInfo(),
                ]);
              
              
                $sentTo[] = $email;

                $invitation->send();
            }
        }

        \Session::flash('messages', "Invitation email(s) sent.");

        return view('admin/users/invites/create')->with([
            'existingInvites' => $existingInvites,
            'existingUsers' => $existingUsers,
            'invalidEmails' => $invalidEmails,
            'sentTo' => $sentTo,
            'groups' => Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
        ]);
    }

    public function resendInvite(Request $request, $hash)
    {
        $invitation = Invitation::where('hash', '=', $hash)->first();

        $logs = Log::create([
            'action'             => 'send invitation',
            'user_id' => Auth::user()->id,
            'message' => $invitation->hash,
            'related_model_type' => 'App\User',
            'related_model_id' => Auth::user()->id,
            'track_info' =>  UserTrackInfo(),
        ]);

        if ($invitation->accepted_at == null)
            $invitation->send();

        \Session::flash('message', "Invitation email resent.");


        return redirect('/admin/users/invites');
    }

    public function bulkResend(Request $request)
    {
        $query = Invitation::whereNull('accepted_at');

        if($request->has('sort'))
            $query = Invitation::orderBy('email', 'asc');
        else
            $query = new Invitation();

        if ($request->has('after')) {
            $query = $query->where('last_sent_at', '>', Carbon::parse($request->after)->toDateTimeString())->orWhere(function($query) use ($request) {
                return $query->whereNull('last_sent_at')->where('sent_at', '>', Carbon::parse($request->after)->toDateTimeString());
            });
        }

        $invitations = $query->get()->filter(function($invitation) {
            return !User::where('email', $invitation->email)->exists();
        });

        return view('admin.users.invites.bulkresend')->with([
            'invitations' => $invitations,
        ]);
    }

    public function postBulkResend(Request $request)
    {
        $invitations = collect($request->invitations);

        $invitations->map(function ($id) {
            return Invitation::find($id);
        })->filter(function ($invitation) {
            if(filter_var($invitation->email, FILTER_VALIDATE_EMAIL))
            {
                $invitation->send();
                return true;
            }
            return false;
        });

        \Session::flash('message', "Successfully resent {$invitations->count()} invitations.");

        return view('admin.users.invites.bulkresend')->with([
            'invitations' => Invitation::whereNull('accepted_at')->get(),
        ]);
    }

    public function cleanup(Request $request)
    {
        return view('admin.users.invites.cleanup');
    }

    public function postCleanup(Request $request)
    {
        $total = 0;

        if ($request->has('start_date') && $request->has('end_date')) {
            $invitations = Invitation::whereNull('accepted_at')
                                    ->whereBetween('sent_at', [Carbon::parse($request->start_date), Carbon::parse($request->end_date)])
                                    ->get();
            $total += $invitations->count();
            $invitations->each(function ($invitation) {
                $invitation->delete();
            });
        } else {
            $invitations = Invitation::whereNull('accepted_at')->get();
            $invitations->each(function ($invitation) use (&$total) {
                if (User::where('email', '=', $invitation->email)->count()) {
                    $invitation->delete();
                    $total++;
                }
            });

            $duplicateEmails = Invitation::selectRaw('email, count(*)')->whereNull('accepted_at')->groupBy('email')->havingRaw('count(*) > 2')->get();

            $duplicateEmails->each(function ($invite) use (&$total) {
                $dupe = Invitation::whereNull('accepted_at')->where('email', '=', $invite->email)->first();
                if ($dupe) {
                    $dupe->delete();
                    $total++;
                }
            });
        }

        

        \Session::flash('message', "Successfully removed {$total} invitations.");

        return view('admin.users.invites.cleanup');
    }

    public function delete($hash)
    {  
        Invitation::where('hash', $hash)->delete();

        return redirect()->back();
    }

}
