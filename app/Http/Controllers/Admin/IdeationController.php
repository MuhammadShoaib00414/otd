<?php

namespace App\Http\Controllers\Admin;

use App\File;
use App\User;
use App\Group;
use App\Ideation;
use App\IdeationPost;
use App\IdeationInvitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Events\Ideations\NewIdeation;
use App\Events\Ideations\IdeationInvite;

class IdeationController extends Controller
{
    public function index()
    {
    	return view('admin.ideations.index')->with([
    		'ideations' => Ideation::orderBy('created_at', 'desc')->get(),
    	]);
    }

    public function closed()
    {
        return view('admin.ideations.index')->with([
            'ideations' => Ideation::onlyTrashed()->orderBy('created_at', 'desc')->get(),
        ]);
    }

    public function edit($id)
    {
    	return view('admin.ideations.edit')->with([
    		'ideation' => Ideation::withTrashed()->find($id),
    		'groups' => Group::whereNull('parent_group_id')->orderBy('name', 'asc')->get(),
    	]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:150',
        ]);

        $ideation = Ideation::withTrashed()->find($id);

        $ideation->update([
            'name' => $request->name,
            'max_participants' => $request->max_participants,
        ]);

        $oldGroups = $ideation->groups;
        $newGroups = collect($request->groups);
        $removedGroupIds = $oldGroups->reject(function ($group) use ($newGroups) {
            return $newGroups->contains($group->id);
        });
        $addedGroupIds = collect($request->groups)->diff($oldGroups->pluck('id'));

        foreach($removedGroupIds as $group) {
            foreach($group->users as $user) {
                IdeationInvitation::where('user_id', $user->id)
                                    ->where('ideation_id', $ideation->id)
                                    ->where('sent_by_id', 0)
                                    ->delete();
            }
        }

        foreach($addedGroupIds as $addedId)
        {
            $group = Group::find($addedId);

            foreach($group->users as $user)
            {
                if(!IdeationInvitation::where('user_id', $user->id)->where('ideation_id', $ideation->id)->count())
                {
                    IdeationInvitation::create([
                        'user_id' => $user->id,
                        'ideation_id' => $ideation->id,
                        'sent_by_id' => 0,
                    ]);
                }
                
            }
        }


        $ideation->groups()->sync($request->groups);
        
        return redirect('/admin/ideations');
    }

    public function show($id)
    {
    	return view('admin.ideations.show.show')->with([
    		'ideation' => Ideation::withTrashed()->find($id),
    	]);
    }

    public function files($id)
    {
    	return view('admin.ideations.show.files')->with([
    		'ideation' => Ideation::withTrashed()->find($id),
    	]);
    }

    public function members($id)
    {
    	return view('admin.ideations.show.members')->with([
    		'ideation' => Ideation::withTrashed()->find($id),
    	]);
    }

    public function invitations($id)
    {
    	return view('admin.ideations.show.invitations')->with([
    		'ideation' => Ideation::withTrashed()->find($id),
    	]);
    }

    public function removeMember($ideationId, $userId)
    {
    	Ideation::withTrashed()->find($ideationId)->participants()->detach($userId);

    	return redirect('/admin/ideations/'.$ideationId.'/members');
    }

    public function invite($id)
    {
    	$ideation = Ideation::withTrashed()->find($id);

    	$users = User::whereNotIn('id', $ideation->participants()->pluck('id'))
    				->whereNotIn('id', $ideation->invited_users->pluck('id'))
    				->get();

    	return view('admin.ideations.show.invite')->with([
    		'ideation' => $ideation,
    		'users' => $users,
    	]);
    }

    public function sendInvite($id, Request $request)
    {
    	IdeationInvitation::create([
    		'user_id' => $request->userId,
    		'ideation_id' => $id,
    		'sent_by_id' => $request->user()->id,
    	]);

        event(new IdeationInvite($request->user(), Ideation::withTrashed()->find($id), 1, collect([$request->userId])));

    	return redirect('/admin/ideations/'.$id.'/invitations');
    }

    public function removeInvitation($ideationId, $userId)
    {
    	Ideation::withTrashed()->find($ideationId)->invitations()->where('user_id', $userId)->delete();

    	return redirect('/admin/ideations/'.$ideationId.'/invitations');
    }

    public function deleteFile($ideationId, $fileId)
    {
        File::find($fileId)->delete();

        return redirect('/admin/ideations/'.$ideationId.'/files');
    }

    public function create()
    {
        return view('admin.ideations.create')->with([
            'groups' => Group::whereNull('parent_group_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:150',
            'body' => 'required',
        ]);

        $ideation = Ideation::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'user_id' => $request->user()->id,
            'proposed_by_id' => $request->user()->id,
            'max_participants' => is_numeric($request->max_participants) ? $request->max_participants : null,
            'is_approved' => 1,
        ]);

        if($request->has('groups'))
        {
            foreach($request->groups as $groupId)
            {
                $group = Group::find($groupId);

                foreach($group->users as $user)
                {
                    IdeationInvitation::create([
                        'user_id' => $user->id,
                        'ideation_id' => $ideation->id,
                        'sent_by_id' => 0,
                    ]);
                }
            }

            $ideation->groups()->sync($request->groups);
        }

        $ideation->participants()->attach($request->user()->id);
        IdeationPost::create([
            'body' => $request->input('body'),
            'ideation_id' => $ideation->id,
            'user_id' => $request->user()->id
        ]);

        event(new NewIdeation($request->user(), $ideation));
        
        return redirect('/admin/ideations/' . $ideation->id);
    }

    public function approvalQueue()
    {
        return view('admin.ideations.approval')->with([
            'ideations' => Ideation::where('is_approved', 0)->orderBy('id', 'desc')->get(),
        ]);
    }

    public function approve(Request $request)
    {

        $ideation = Ideation::find($request->ideation);
    
        $ideation->update(['is_approved' => 1]);
        if(!$ideation->has_max_participants)
        {
           
            if(!$ideation->invitations()->where('user_id', $ideation->user_id)->count()){
                
                IdeationInvitation::create([
                    'user_id' => $request->user()->id,
                    'ideation_id' => $ideation->id,
                    'sent_by_id' => $ideation->user_id
                 
                ]);
                $ideation->invitations()->create([
                    'user_id' => $ideation->user_id,
                    'ideation_id' => $ideation->id,
                ]);
            }
        }

        $notification = $ideation->owner->notifications()->create([
            'notifiable_type' => 'App\Ideation',
            'notifiable_id' => $request->ideation,
            'action' => 'Ideation Approved',
        ]);

        return redirect(url()->previous());
    }

    public function reject(Request $request)
    {
        $ideation = Ideation::find($request->ideation);
        $notification = $ideation->owner->notifications()->create([
            'notifiable_type' => 'App\Ideation',
            'notifiable_id' => $request->ideation,
            'message' => $request->message,
            'action' => 'Ideation Not Accepted',
        ]);
        $ideation->notifications()->where('id', '!=', $notification->id)->delete();
        $ideation->delete();

        return redirect(url()->previous());
    }

    public function restore($ideationId)
    {
        Ideation::withTrashed()->find($ideationId)->restore();

        return redirect('/admin/ideations/'.$ideationId);
    }
}
