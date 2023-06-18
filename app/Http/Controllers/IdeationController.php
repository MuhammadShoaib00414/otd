<?php

namespace App\Http\Controllers;

use App\File;
use App\Post;
use App\Group;
use App\Ideation;
use App\VideoRoom;
use App\IdeationPost;
use App\ReportedPost;
use App\IdeationSurvey;
use App\IdeationArticle;
use App\IdeationInvitation;
use Illuminate\Support\Str;
use App\Events\PostReported;
use Illuminate\Http\Request;
use App\Events\IdeationProposed;
use Illuminate\Support\Facades\DB;
use App\Events\Ideations\NewIdeation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Events\Ideations\IdeationInvite;
use App\Events\Ideations\IdeationViewed;
use App\Events\Ideations\IdeationDeleted;
use App\Events\Ideations\IdeationReplied;

class IdeationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'ideation']);
        Cache::flush();
    }

    public function index(Request $request)
    {
        $joinedCount = Ideation::orderBy('created_at', 'desc')
                            ->whereHas('participants', function ($query) use ($request) {
                                $query->where('users.id', '=', $request->user()->id);
                            }, '=', 1)->count();

        if ($joinedCount > 0)
            return redirect('/ideations/joined');
        else
            return redirect('/ideations/invited');
    }

    public function invited(Request $request)
    {
        $ideations = Ideation::orderBy('created_at', 'desc')
                            //->where('ideations.is_approved', '=', 1)
                            // ->whereHas('groups', function ($query) use ($request) {
                            //     $query->whereIn('groups.id', $request->user()->groups->pluck('id'));
                            // }, '>', 0)
                            ->whereHas('participants', function ($query) use ($request) {
                                $query->where('ideation_user.user_id', '=', $request->user()->id);
                            }, '=', 0)
                            ->whereHas('invitations', function ($query) use ($request) {
                                $query->where('ideation_invitations.user_id', '=', $request->user()->id);
                            })
                            ->notFull()
                            ->paginate();
                        
        $proposedCount = Ideation::where('is_approved', '=', 0)->count();

        return view('ideations.invited')->with([
            'ideations' => $ideations,
            'proposedCount' => $proposedCount,
            'user_id' => $request->user()->id,
        ]);
    }

    public function joined(Request $request)
    {
       
        $ideations = Ideation::orderBy('created_at', 'desc')->where('is_approved', '=', 1)
                            ->whereHas('participants', function ($query) use ($request) {
                                $query->where('users.id', '=', $request->user()->id);
                            }, '=', 1)->simplePaginate();
                           
        $proposedCount = Ideation::where('is_approved', '=', 0)->count();

        return view('ideations.joined')->with([
            'ideations' => $ideations,
            'proposedCount' => $proposedCount,
        ]);
    }


    public function show($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        if($ideation) {
            $invitation = $ideation->invitations()->where('user_id', $request->user()->id)->first();

            if($invitation && !$invitation->read_at) {
                $ideation->invitations()->where('user_id', $request->user()->id)->first()->update(['read_at' => \Carbon\Carbon::now()->toDateTimeString()]);
                
                event(new IdeationViewed($request->user(), $ideation));
            } else if($ideation->notificationsFor($request->user()->id)->count()) {
                event(new IdeationViewed($request->user(), $ideation));
            }

            return view('ideations.show')->with([
                'ideation' => $ideation,
            ]);
        } else {
            return errorView('This discussion thread no longer exists.');
        }
            
    }

    public function propose(Request $request)
    {
        $groups = $request->user()->groups;

        return view('ideations.propose')->with([
            'groups' => $groups,
        ]);
    }

    public function submitProposal(Request $request)
    {
        $request->validate([
            'name' => 'required|max:250',
        ]);

        $ideation = Ideation::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'user_id' => $request->user()->id,
            'proposed_by_id' => $request->user()->id,
            'is_approved' => ($request->user()->is_admin || $request->user()->is_group_admin || !getSetting('is_ideation_approval_enabled')),
        ]);
        $ideation->update([
            'slug' => Str::slug($request->name, '-') . "-" . $ideation->id
        ]);
        $ideation->participants()->attach($request->user()->id);
        $ideation->groups()->sync($request->groups);
        event(new IdeationProposed($ideation));

        IdeationPost::create([
            'body' => $request->input('body'),
            'ideation_id' => $ideation->id,
            'user_id' => $request->user()->id
        ]);

        return redirect('/ideations/'.$ideation->slug);
    }

    public function decline($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        $notification = $ideation->owner->notifications()->create([
            'notifiable_type' => 'App\Ideation',
            'notifiable_id' => $ideation->id,
            'message' => $request->message,
            'action' => 'Ideation Not Accepted',
        ]);

        $notifications = $ideation->notifications()->where('notifications.id', '!=', $notification->id)->get();
        $notifications->map(function ($notification) {
            $notification->delete();
        });

        $ideation->delete();

        if(Ideation::where('is_approved', 0)->count())
            return redirect('/ideations/proposed');
        else
            return redirect('/ideations/joined');

    }

    public function create(Request $request)
    {
        $groups = collect([]);

        $groups = Group::orderBy('name', 'desc')->whereNull('parent_group_id')->get();

        if ($groups->count() == 0)
            return redirect('/ideations');

        return view('ideations.create')->with([
            'groups' => $groups,
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
            'is_approved' => ($request->user()->is_admin || $request->user()->is_group_admin || !getSetting('is_ideation_approval_enabled')),
        ]);

        $ideation->update([
            'slug' => Str::slug($request->name, '-') . "-" . $ideation->id
        ]);
        $ideation->groups()->attach($request->groups);

        if($request->has('groups'))
        {
            foreach($request->groups as $groupId)
            {
                $group = Group::find($groupId);
                foreach($group->users as $user)
                {
                    //invite user if there isn't already that invite
                    //store 0 for sent_by_id
                    if(!IdeationInvitation::where('user_id', $user->id)->where('ideation_id', $ideation->id)->count())
                    {
                        IdeationInvitation::create([
                            'ideation_id' => $ideation->id,
                            'user_id' => $user->id,
                            'sent_by_id' => 0,
                        ]);
                    }
                }
            }
        }

        if($request->has('invite'))
        {
            foreach($request->invite as $userId)
            {
                $ideation->invitations()->create(['user_id' => $userId, 'sent_by_id' => $request->user()->id]);
            }
        }

        $ideation->participants()->attach($request->user()->id);
        IdeationPost::create([
            'body' => $request->input('body'),
            'ideation_id' => $ideation->id,
            'user_id' => $request->user()->id
        ]);

        event(new NewIdeation($request->user(), $ideation));

        return redirect('/ideations');
    }


    public function review($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $groups = collect([]);
        if ($request->user()->is_admin)
            $groups = Group::orderBy('name', 'desc')->whereNull('parent_group_id')->get();
        else
            $groups =  $request->user()->groups()->whereNull('parent_group_id')->where('group_user.is_admin', '=', 1)->get();
        
        if ($groups->count() == 0)
            return redirect('/ideations');

        return view('ideations.review')->with([
            'ideation' => $ideation,
            'groups' => $groups,
        ]);
    }

    public function approve($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $ideation->update([
            'is_approved' => 1,
            'max_participants' => is_numeric($request->max_participants) ? $request->max_participants : null,
        ]);
        $ideation->groups()->attach($request->groups);
        $post = Post::create([
            'post_type' => get_class($ideation),
            'post_id' => $ideation->id,
        ]);
        $post->groups()->attach($request->groups);

        $notification = $ideation->owner->notifications()->create([
            'notifiable_type' => 'App\Ideation',
            'notifiable_id' => $ideation->id,
            'action' => 'Ideation Approved',
        ]);

        return redirect('/ideations/'.$slug);
    }

    public function edit($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        return view('ideations.edit')->with([
            'ideation' => $ideation,
            'groups' => Group::orderBy('name', 'asc')->whereNull('parent_group_id')->get(),
        ]);
    }

    public function update($slug, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|max:150',
        ]);

        $ideation = Ideation::where('slug', '=', $slug)->first();

        if (is_numeric($request->max_participants) && $request->max_participants < $ideation->participants()->count())
            return redirect()->back()->with('participants-error', 'You cannot set the max participants to less than are currently in the ideaiton. If needed, remove users before updating the max participants.');

        $ideation->update([
            'name' => $request->name,
            'max_participants' => $request->max_participants,
        ]);

        $ideationGroups = $ideation->groups;

        $removedGroupIds = $ideationGroups->pluck('id')->diff($request->groups);
        $addedGroupIds = collect($request->groups)->diff($ideationGroups->pluck('id'));

        foreach($removedGroupIds as $removedId)
        {
            $group = Group::find($removedId);
            foreach($group->users as $user)
            {
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
        
        return redirect('/ideations/' . $slug);
    }

    public function delete($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        $ideation->invitations()->delete();

        $ideation->notifications()->delete();

        $ideation->delete();

        event(new IdeationDeleted($request->user(), $ideation));

        return redirect('/ideations');
    }

    public function postReply($slug, Request $request)
    {
        $validatedData = $request->validate([
            'body' => 'required|max:10000',
        ]);

        $ideation = Ideation::where('slug', '=', $slug)->first();

        IdeationPost::create([
            'body' => $request->input('body'),
            'ideation_id' => $ideation->id,
            'user_id' => $request->user()->id
        ]);

        event(new IdeationReplied($request->user(), $ideation));

        return redirect('/ideations/' . $slug);
    }

    public function editPost($slug, $postId, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $post = IdeationPost::find($postId);

        return view('ideations.posts.edit')->with([
            'ideation' => $ideation,
            'post' => $post,
        ]);
    }

    public function updatePost($slug, $postId, Request $request)
    {
        $validation = $request->validate([
            'body' => 'required',
        ]);

        $post = IdeationPost::find($postId);
        $post->update([
            'body' => $request->body,
        ]);

        return redirect('/ideations/' . $slug);
    }

    public function deletePost($slug, $postId, Request $request)
    {
        $post = IdeationPost::find($postId);
        $post->reported()->delete();
        $post->delete();

        return redirect('/ideations/' . $slug);
    }

    public function join($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        if($ideation->has_max_participants)
            return redirect('/ideations/invited')->with('error', 'Oops! This ideation is at maximum number of participants.');
        
        $ideation->participants()->syncWithoutDetaching($request->user()->id);
        IdeationInvitation::where('user_id', $request->user()->id)->where('ideation_id', $ideation->id)->delete();

        return redirect('/ideations/' . $slug);
    }

    public function leave($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $ideation->participants()->detach($request->user()->id);

        return redirect('/ideations/');
    }

    public function listProposed(Request $request)
    {
        $ideations = Ideation::orderBy('created_at', 'desc')->where('is_approved', '=', 0)->simplePaginate();

        return view('ideations.proposed')->with([
            'ideations' => $ideations,
        ]);
    }

    public function filesIndex($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        return view('ideations.files')->with([
            'ideation' => $ideation,
            'files' => $ideation->files,
        ]);
    }

    public function uploadFile($slug, Request $request)
    {
        $validation = $request->validate([
            'document' => 'required|file|max:51200',
        ]);

        $ideation = Ideation::where('slug', '=', $slug)->first();
        if ($request->has('document')) {
            $file = $request->file('document');
            $path = $request->file('document')->store('ideations/'.$slug, 's3');

            File::create([
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'ideation_id' => $ideation->id,
            ]);

            return redirect('/ideations/'.$slug.'/files');
        }
    }

    public function deleteFile($slug, $file, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $file = File::find($file);
        $file->delete();

        return redirect('/ideations/'.$slug.'/files');
    }

    public function downloadFile($slug, $file, Request $request)
    {
        $file = File::find($file);

        $mimeType = Storage::disk('s3')->mimeType($file->getRawOriginal('path'));

        $headers = [
            'Content-Type'        => $mimeType,
            'Content-Disposition' => 'attachment; filename="'. $file->name .'"',
        ];
 
        return \Response::make(Storage::disk('s3')->get($file->getRawOriginal('path')), 200, $headers);
    }

    public function membersIndex($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        return view('ideations.members')->with([
            'ideation' => $ideation,
        ]);
    }

    public function invite($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        if(!$ideation->has_max_participants)
        {
            foreach($request->invite as $id) {
                if(!$ideation->invitations()->where('user_id', $id)->count()){
                    $ideation->invitations()->create([
                        'user_id' => $id,
                        'sent_by_id' => $request->user()->id,
                    ]);
                }
            }

            event(new IdeationInvite($request->user(), $ideation, count($request->invite), $request->invite));
        }
        else
            return redirect('/ideations/'.$slug);
        
        
        return redirect('/ideations/'.$slug.'/members');
    }

    public function removeMember($slug, $id, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();
        $ideation->participants()->detach($id);

        return redirect('/ideations/'.$slug.'/members');
    }

    public function articlesIndex($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        return view('ideations.articles')->with([
            'ideation' => $ideation,
        ]);
    }

    public function addArticle($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        if(!$request->input('image'))
            return redirect()->back()->withErrors(['Sorry, this image url is invalid.']);

        if(strpos(getRemoteMimeType($request->input('image')), 'html'))
                return redirect()->back()->withErrors(['Error: URL blocks this file from being used.']);
            try
            {
                $opts = array('http' =>
                    array(
                        'method'  => 'GET',
                        'ignore_errors' => TRUE,
                    )
                );
                $context = stream_context_create($opts);
                $contents = file_get_contents($request->input('image'), false, $context);
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('invalid-image', 'Error: image url is invalid.');
            }
            $name = "images/".time();
            if(strpos($name, '?'))
                $name = substr($name, 0, strpos($name, "?"));
            $file = Storage::put($name, $contents);
            // optimizeImage(public_path() . '/uploads/' . $name, 1800);

        $article = IdeationArticle::create([
            'title' => $request->input('title'),
            'image_url' => $name,
            'url' => $request->input('url'),
            'ideation_id' => $ideation->id,
        ]);

        return redirect('/ideations/'.$slug.'/articles');
    }

    public function deleteArticle($slug, $articleId, Request $request)
    {
        $article = IdeationArticle::find($articleId);
        $article->delete();

        return redirect('/ideations/'.$slug.'/articles');
    }

    public function surveysIndex($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        return view('ideations.surveys')->with([
            'ideation' => $ideation,
        ]);
    }

    public function addSurvey($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        $article = IdeationSurvey::create([
            'title' => $request->input('title'),
            'image_url' => $request->input('image'),
            'url' => $request->input('url'),
            'ideation_id' => $ideation->id,
        ]);

        return redirect('/ideations/'.$slug.'/surveys');
    }

    public function deleteSurvey($slug, $articleId, Request $request)
    {
        $article = IdeationSurvey::find($articleId);
        $article->delete();

        return redirect('/ideations/'.$slug.'/surveys');
    }

    public function viewInvitation($slug, Request $request)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        $ideation->invitations()->where('user_id', $request->user()->id)->update(['read_at' => \Carbon\Carbon::now()]);

        return redirect('/ideations/' . $slug);
    }

    public function report(Request $request, $slug, $postId)
    {
        if(!IdeationPost::find($postId)->reported()->count())
        {
            ReportedPost::create([
                'postable_id' => $postId,
                'postable_type' => 'App\IdeationPost',
                'reported_by' => $request->user()->id,
            ]);
        }
        else
        {
            ReportedPost::where('postable_id', $postId)->where('postable_type', 'App\IdeationPost')->update(['resolved_by' => null]);
        }

        event(new PostReported(Ideation::where('slug', $slug)->first(), $postId, $request->user()->name));

        return redirect(url()->previous());
    }

    public function resolve(Request $request, $slug, $postId)
    {
        IdeationPost::find($postId)->reported()->update(['resolved_by' => $request->user()->id]);

        return redirect(url()->previous());
    }

    public function reportArticle(Request $request, $slug, $articleId)
    {
        if(!IdeationArticle::find($articleId)->reported()->count()) {
            ReportedPost::create([
                'postable_id' => $articleId,
                'postable_type' => 'App\IdeationArticle',
                'reported_by' => $request->user()->id,
            ]);
        } else {
            ReportedPost::where('postable_id', $articleId)->where('postable_type', 'App\IdeationArticle')->update(['resolved_by' => null]);
        }

        event(new PostReported(Ideation::where('slug', $slug)->first(), $articleId, $request->user()->name, true));

        return redirect(url()->previous());
    }

    public function resolveArticle(Request $request, $slug, $articleId)
    {
        if(IdeationArticle::find($articleId)->reported()->count())
            ReportedPost::where('postable_id', $articleId)->where('postable_type', 'App\IdeationArticle')->update(['resolved_by' => $request->user()->id]);

        return redirect(url()->previous());

    }

    public function videoConference(Request $request, $slug)
    {
        $ideation = Ideation::where('slug', '=', $slug)->first();

        $videoRoom = VideoRoom::updateOrCreate([
                'attachable_type' => 'App\Ideation',
                'attachable_id' => $ideation->id,
            ],
            ['is_enabled' => 1]
        );


        return view('ideations.video')->with([
            'ideation' => $ideation,
        ]);
    }
}
