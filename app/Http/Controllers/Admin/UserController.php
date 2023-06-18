<?php

namespace App\Http\Controllers\Admin;

use Mail;
use App\Log;
use App\User;
use App\Group;
use App\Receipt;
use App\Setting;
use App\Question;
use Carbon\Carbon;
use App\Invitation;
use App\Notification;
use App\DiscussionPost;
use App\Mail\InviteUser;
use App\DiscussionThread;
use Illuminate\Support\Str;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UserController extends Controller
{
    public $broker;
    public $groups;
    public function __construct()
    {
        $this->middleware('auth');
        $this->broker = \Illuminate\Support\Facades\Password::broker();
        Cache::flush();
    }

    public function index(Request $request)
    {
    
      if ($request->has('q')) {
            $query = $request->input('q');
         
            $users = User::where(function ($eQuery) use ($query) {
                        $eQuery->where('name', 'LIKE', '%' . $query . '%')
                              ->orWhere('job_title', 'LIKE', '%' . $query . '%')
                              ->orWhere('company', 'LIKE', '%' . $query . '%')
                              ->orWhere('location', 'LIKE', '%' . $query . '%')
                              ->orWhere('email', 'LIKE', '%' . $query . '%');
                    });
                   
        } else {
            $users = User::orderBy('name', 'asc');
        }

        if ($request->has('filter')) {
            if ($request->input('filter') == 'enabled')
                $users->where('is_enabled', '=', 1);
            else if ($request->input('filter') == 'disabled')
                $users->where('is_enabled', '=', 0);
            else if($request->input('filter') == 'deleted')
                $users->withTrashed()->whereNotNull('deleted_at');
            else if ($request->input('filter') == 'gdpr')
                $users->withTrashed()->where('is_hidden', '=', 1);
        }

        $registration_link = Setting::where('name', 'registration_key')->first();
        $is_open_regis = Setting::where('name', 'open_registration')->first();

        if($registration_link && $is_open_regis && $is_open_regis->value)
            $registration_link = env('APP_URL') . '/register/' . $registration_link->value;
        else
            $registration_link = false;

        $allUsers = clone $users;
        $users->get();
        return view('admin.users.index')->with([
            'users' => $users->paginate(50),
            'registration_link' => $registration_link,
            'allUsers' => $allUsers->select('id', 'name')->get(),
        ]);
    }

    public function show($id, Request $request)
    {
        $user = User::where('id',$id)->withTrashed()->first();

        return view('admin.users.show')->with([
            'user' => $user,
        ]);
    }

    public function invite(Request $request)
    {
        if(isset($_GET['sort']))
            $invitations = Invitation::orderBy('email', 'asc')->get();
        else
            $invitations = Invitation::all();


        return view('admin.users.invite')->with([
            'invitations' => $invitations,
        ]);
    }

    public function sendInvite(Request $request)
    {
        $emails = array_unique(explode("\r\n", $request->input('emails')));

        foreach ($emails as $email) {
            // TODO -> Send an email linking (with stored hash) in the url for invitation
            $invitation = Invitation::create([
                'email' => $email,
                'custom_message' => $request->input('custom_message'),
                'sent_at' => Carbon::now(),
                'hash' => Str::random(7),
            ]);

            Mail::to($email)->send(new InviteUser($invitation));
        }

        \Session::flash('message', "Invitation email(s) sent.");

        return redirect('admin/users/invite');
    }

    public function resendInvite(Request $request, $hash)
    {
        $invitation = Invitation::where('hash', '=', $hash)->first();

        if ($invitation->accepted_at == null)
            Mail::to($invitation->email)->send(new InviteUser($invitation));

        \Session::flash('message', "Invitation email resent.");

        return redirect('/admin/users/invite');
    }

    public function edit($id, Request $request)
    {
        $user = User::find($id);

        return view('admin.users.edit')->with([
            'user' => User::find($id),
        ]);
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);
        $user->update([
            'is_admin' => $request->has('is_admin'),
            'is_enabled' => $request->is_enabled == "true" ? 1 : 0,
            'is_hidden' => $request->has('is_hidden'),
            'is_event_only' => $request->has('is_event_only'),
        ]);

        $user->updateSearch();

        if($request->has('titles')) {
            foreach($request->titles as $titleId => $userId) {
                if ($userId && User::where('id', $userId)->count())
                    $user->titles()->syncWithoutDetaching([$titleId => ['assigned_id' => $userId]]);
                else
                    $user->titles()->detach($titleId);
            }
        }

        return redirect('/admin/users/' . $id);
    }

    public function showGroups($id, Request $request)
    {
        return view('admin.users.groups')->with([
            'user' => User::find($id),
            'groups' => Group::whereNull('deleted_at')->whereNull('parent_group_id')->get(),
        ]);
    }

    public function updateGroups($id, Request $request)
    {
        if (!$request->groups)
            return back()->with('error', 'User must be a member of at least one group.');;

        $user = User::find($id);

        $adminGroups = $request->has('groupsIsAdminOf') ? $request->groupsIsAdminOf : [];

        $groups = collect($request->groups)->map(function ($groupId) use ($adminGroups) {
            return ['group_id' => $groupId, 'is_admin' => (in_array($groupId, $adminGroups)) ? 1 : 0];
        });

        $user->groups()->sync($groups);

        return redirect("/admin/users/{$id}/groups");
    }

    public function showActivity($id, Request $request)
    {
        $user = User::where('id',$id)->withTrashed()->first();

        $logs = $user->logs()->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.users.activity')->with([
            'user' => $user,
            'logs' => $logs,
        ]);
    }

    public function downloadUsersCsv(Excel $excel,Request $request)
    {
        ini_set('memory_limit','1024M');
        set_time_limit(0);
        //return $excel->download(new UsersExport, 'users.xlsx');
         $users = User::get();
         $this->groups = Group::get();
        return (new FastExcel($users))->download('users.csv', function ($user) {
            $name  = splitName($user->name);
            if($name[0] != ''){
                $fname = $name[0];
            }else{
                $fname = '-';
            }
            if($name[1] != ''){
                $lname = $name[1];
            }else{
                $lname = '-';
            }
            $rowData = [
                'id' => $user->id,
                'First Name' =>  $fname,
                'Last Name' =>  $lname,
                'email' => $user->email,
                'admin' => $user->is_admin ? 'true' : '',
                'join date' => $user->created_at ? $user->created_at->format('Y-m-d') : '',
                'job title' => $user->job_title,
                'summary' => $user->summary,
                'company' => $user->company,
                'location' => $user->location,
                'twitter' => $user->twitter,
                'instagram' => $user->instagram,
                'facebook' => $user->facebook,
                'linkedin' => $user->linkedin,
                'website' => $user->website,
                'disabled' => !$user->is_enabled ? 'true' : '',
                'superpower' => $user->superpower,
                'total points' => $user->points_total,
            ];
            foreach ($this->groups as $key => $group) {
                $rowData[$group->name] = (in_array($group->name, $user->groupsName()->toArray())) ? 'TRUE' : '';
            }
            return $rowData;
        });

    }

    public function iterateAllUsers()
    {
        $user = User::get();
        // foreach (User::cursor() as $user) {
        //     yield $user;
        // }
    }
    
    // public function downloadUsersCsv(Excel $excel)
    // {
    //     //return $excel->download(new UsersExport, 'users.xlsx');

    //     return (new FastExcel($this->iterateAllUsers()))->download('users.csv', function ($user) {
    //         return [
    //             'id' => $user->id,
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'admin' => $user->is_admin ? 'true' : '',
    //             'join date' => $user->created_at ? $user->created_at->format('Y-m-d') : '',
    //             'job title' => $user->job_title,
    //             'summary' => $user->summary,
    //             'company' => $user->company,
    //             'location' => $user->location,
    //             'twitter' => $user->twitter,
    //             'instagram' => $user->instagram,
    //             'facebook' => $user->facebook,
    //             'linkedin' => $user->linkedin,
    //             'website' => $user->website,
    //             'disabled' => !$user->is_enabled ? 'true' : '',
    //             'superpower' => $user->superpower,
    //             'total points' => $user->points_total,
    //             'mentor status' => $user->is_mentor ? 'true' : '',
    //         ];
    //     });
    // }

    // public function iterateAllUsers()
    // {
    //     foreach (User::cursor() as $user) {
    //         yield $user;
    //     }
    // }

    public function delete($user)
    {
        $user = User::find($user);

        // Notification::where('notifiable_type', 'App\MessageThread')
        //             ->whereIn('notifiable_id', $user->threads()->pluck('message_threads.id'))
        //             ->delete();

        // Notification::where('notifiable_type', 'App\Shoutout')
        //             ->whereIn('notifiable_id', $user->shoutouts()->pluck('id'))
        //             ->delete();

        // Notification::where('notifiable_type', 'App\Introduction')
        //             ->whereIn('notifiable_id', $user->introductions()->pluck('id'))
        //             ->delete();
        
        $user->update([
            'deleted_at' => Carbon::now(),
            'is_hidden' => 1,
            'is_enabled' => 0,
        ]);

        return redirect('/admin/users');
    }

    public function getResetLink(User $user)
    {
        return url(config('app.url').route('password.reset', ['token' => $this->createToken($user), 'email' => $user->email], false));
    }

    public function generateResetLink($userId)
    {
        $user = User::find($userId);
        $this->createToken($user);
        return redirect('/admin/users/' . $user->id);
    }

    public function tokenExists($email)
    {
        return count(DB::select('select token from password_resets where email = "' . $email . '"'));
    }

    public function createToken($user)
    {
        return $this->broker->createToken($user);
    }

    public function auth(User $user, Request $request)
    {
        $request->user()->logs()->create([
            'action'             => 'logged in as user',
            'related_model_type' => User::class,
            'related_model_id'   => $user->id,
        ]);

        $oldUser = $request->user();
        // store oldUser in session
        \Session::put('oldUser', $oldUser); 
        Auth::login($user);

        return redirect('/home');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->find($id);
        $user->restore();
        $user->update([
            'is_enabled' => 1,
        ]);
        return redirect('/admin/users/'. $id);
    }

    public function notify($userId)
    {
        Artisan::call('event:new --user=' . $userId);

        return redirect('/admin/users/'.$userId);
    }

    public function indexPurchases($userId)
    {
        return view('admin.users.receipts.index')->with([
            'user' => User::find($userId),
        ]);
    }

    public function showReceipt($userId, $receiptId)
    {
        return view('admin.users.receipts.show')->with([
            'user' => User::find($userId),
            'receipt' => Receipt::find($receiptId),
        ]);
    }

    public function editCategories($id, Request $request)
    {
        $user = User::find($id);

        return view('admin.users.categories')->with([
            'user' => $user,
        ]);
    }

    public function updateCategories($id, Request $request)
    {
        $user = User::find($id);
        $user->options()->sync($request->options);

        return redirect('/admin/users/'.$id.'/categories')->with('success', " Users' categories saved.");
    }

    public function clearExtraSoftDeletedUsers(Request $request)
    {
        if($request->user()->email != 'davis@ipx.org')
            return redirect('/home');

        $ids = User::where('is_hidden', 1)->where('name', 'LIKE', '%(deleted)')->pluck('id');
        $ids = $ids->merge(User::onlyTrashed()->pluck('id'));
        $count = $ids->count();
        User::withTrashed()->whereIn('id', $ids)->chunk(20, function($users) {
            foreach($users as $user)
            {
                $new_email = 'deleted' . \Carbon\Carbon::now()->format('mdy') . $user->email;

                $user->update([
                    'email' => $new_email,
                ]);
            }
        });

        return redirect('/admin/users');
        // ->chunk(50, function ($users) {
        //     foreach($users as $user)
        //     {
        //         $user->groups()->sync([]);
        //         $user->options()->sync([]);
        //         $user->sentMessages()->delete();
        //         $user->threads()->sync([]);
        //         $user->awardedPoints()->delete();
        //         $user->introductions()->sync([]);
        //         $user->discussionPosts()->withTrashed()->forceDelete();
        //         $discussionIds = $user->discussionThreads()->pluck('id');
        //         DiscussionThread::whereIn('id', $discussionIds)->chunk(5, function($threads) {
        //             foreach($threads as $thread)
        //             {
        //                 $thread->posts()->forceDelete();
        //             }
        //         });
        //         $user->logs()->delete();
        //         $user->ideationInvitations()->delete();
        //         $user->titles()->sync([]);
        //         $user->badges()->sync([]);
        //         $user->rsvps()->delete();
        //         $user->ownedEvents()->forceDelete();
        //         $user->shoutouts()->delete();
        //         $user->receivedShoutouts()->delete();
        //         $user->textPosts()->delete();
        //         $user->forceDelete();
        //     }
        // });

        return redirect('/admin/users')->with('status', $count . ' users permanently deleted.');
    }

    public function BulkDeleteView(Request $request)
    {
       \Session::flash('message', "");

        return view('admin.users.bulkdelete')->with([
            'existingInvites' => collect(),
            'existingUsers' => collect(),
            'invalidEmails' => collect(),
            'sentTo' => collect(),
            'groups' => Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
        ]);
    }
    public function bulkDeleteUsers(Request $request)
    {
      set_time_limit(0);
      $authUser = auth()->user();
      $validEmails = array_unique(explode("\r\n", $request->input('valid_emails')));
      if (sizeof($validEmails) == 1 && !filter_var($validEmails[0], FILTER_VALIDATE_EMAIL)) {
        \Session::flash('message', "Please provided email valid.");
        return view('admin/users/bulkdelete');
      }
      $emails = collect($validEmails);
      $invalidEmails = collect();
      foreach ($emails as $email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $user = User::where('email', $email)->first();
        $invalidEmails = [];
        if($user) {
            $user->delete();
            $insertData  = Log::create([
                'user_id'           => $user->id,
                'action'             => 'Account Deleted',
                'message'             => 'Account Deleted By '.$authUser->name.' ',
                'related_model_type' => User::class,
                'related_model_id'   => $authUser->id,
                ]);
        } else {
            $invalidEmails[] = $email;
        }
    }

       \Session::flash('message', "The emails provided are now deleted.");
        return view('admin/users/bulkdelete');
    }
    public function BulkDeleteConformation(Request $request)
    {
        $emails = array_unique(explode("\r\n", $request->input('emails')));
        $notExistingUser = collect();
        $existingUsers = collect();
        $invalidEmails = collect();
        $notExistingUsers = collect();
        $deleted = collect();
        foreach ($emails as $email) {
            $email = preg_replace('/\s+/', '', $email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
              continue;
            }
            if (User::where('email', '=', $email)->count())
                $existingUsers[] = $email;
            else if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $email == '')
                $invalidEmails[] = $email;
            else if (User::where('email', '!=', $email)->count())
                  $notExistingUsers[] = $email;
            else {
                $deleted[] = $email;
                foreach($existingUsers as $deleteAt){

                   $users =  DB::table('users')->whereIn('email', [$deleteAt])
                    ->update([
                        'deleted_at' => now()
                    ]);
                }
            }
        }

        // \Session::flash('message', "Invitation email(s) sent.");
       \Session::flash('message', "");

        return view('admin/users/bulk-delete-conformation')->with([
            'existingUsers' => $existingUsers,
            'invalidEmails' => $invalidEmails,
            'sentTo' => $deleted,
            'notExistingUsers' => $notExistingUsers,
        ]);
    }
}
