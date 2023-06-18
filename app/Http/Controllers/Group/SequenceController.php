<?php

namespace App\Http\Controllers\Group;

use App\Log;
use App\ArticlePost;
use App\Exports\ContentExport;
use App\Group;
use App\Http\Controllers\Controller;
use App\Module;
use App\ModuleUser;
use App\Post;
use App\SequenceUser;
use App\Shoutout;
use Carbon\Carbon;
use Embed\Embed;
use Illuminate\Http\Request;
use App\Events\ShoutoutMade;
use Illuminate\Support\Facades\Storage;

class SequenceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'group']);
    }
    
    public function show(Group $group, Request $request)
    {
        if (!$group->sequence)
            return back();

        $sequenceUser = SequenceUser::where([
            'user_id' => $request->user()->id,
            'sequence_id' => $group->sequence->id,
        ])->first();

        if (!$sequenceUser) {
            $sequenceUser = SequenceUser::create([
                'user_id' => $request->user()->id,
                'sequence_id' => $group->sequence->id,
                'last_completed_module_id' => 0,
            ]);
        }

        $modules = $group->sequence->modules()->orderBy('order_key', 'asc')->get();
        $nextShouldBeUnavailable = false;
        $user = $request->user();
        foreach ($modules as $key => $module) {
            if($module->hasUserCompleted($user))
                $module->is_available = true;
            elseif($key == 0)
            {
                $module->is_available = true;
                $nextShouldBeUnavailable = true;
            }
            elseif(!$nextShouldBeUnavailable)
            {
                $module->is_available = true;
                $nextShouldBeUnavailable = true;
            }
            else
                $module->is_available = false;
        }

        return view('groups.sequence.show')->with([
            'group' => $group,
            'sequence' => $group->sequence,
            'sequenceUser' => $sequenceUser,
            'modules' => $modules,
        ]);
    }

    public function createModule(Group $group)
    {
        return view('groups.sequence.module.create')->with([
            'group' => $group,
        ]);
    }

    public function storeModule(Group $group, Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        if ($request->has('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('module-images', 's3');
            // optimizeImage(public_path() . '/uploads/' . $thumbnailPath, 1000);
        } else {
            $thumbnailPath = null;
        }

        $module = Module::create([
            'name' => $request->name,
            'thumbnail_image_path' => $thumbnailPath,
            'content' => $request->content,
            'sequence_id' => $group->sequence->id,
            'order_key' => $group->sequence->modules->count() + 1
        ]);

        return redirect('groups/'. $group->slug . '/sequence');
    }

    public function reorderModules(Group $group, Request $request)
    {
        return view('groups.sequence.module.reorder')->with([
            'group' => $group,
            'sequence' => $group->sequence,
            'modules' => $group->sequence->modules()->orderBy('order_key', 'asc')->get(),
        ]);
    }

    public function postReorderModules(Group $group, Request $request)
    {
        foreach($request->modules as $moduleId => $module) {
            Module::where('id', $moduleId)->update(['order_key' => $module['order_key']]);
        }

        return redirect('/groups/'.$group->slug.'/sequence');
    }

    public function showModule(Group $group, Module $module, Request $request)
    {
        $moduleUser = ModuleUser::where([
            'user_id' => $request->user()->id,
            'module_id' => $module->id,
        ])->first();

        $sequenceUser = SequenceUser::where([
            'user_id' => $request->user()->id,
            'sequence_id' => $group->sequence->id,
        ])->first();

        if (!$moduleUser) {
            $moduleUser = ModuleUser::create([
                'user_id' => $request->user()->id,
                'module_id' => $module->id,
                'started_at' => \DB::raw('NOW()'),
            ]);
        }

        if (!$sequenceUser) {
            $sequenceUser = SequenceUser::create([
                'user_id' => $request->user()->id,
                'sequence_id' => $group->sequence->id,
                'started_at' => \DB::raw('NOW()'),
                'last_completed_module_id' => 0,
            ]);
        } else if ($sequenceUser->started_at == null) {
            $sequenceUser->update([
                'started_at' => \DB::raw('NOW()'),
            ]);
        }

        return view('groups.sequence.module.show')->with([
            'group' => $group,
            'sequence' => $group->sequence,
            'module' => $module,
            'moduleUser' => $moduleUser,
            'sequenceUser' => $sequenceUser,
        ]);
    }

    public function editModule(Group $group, Module $module)
    {
        return view('groups.sequence.module.edit')->with([
            'group' => $group,
            'sequence' => $group->sequence,
            'module' => $module,
        ]);
    }

    public function updateModule(Group $group, Module $module, Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $changesArray =  [
            'name' => $request->name,
            'content' => $request->content,
            'sequence_id' => $group->sequence->id,
        ];

        if ($request->has('thumbnail')) {
            $changesArray['thumbnail_image_path'] = $request->file('thumbnail')->store('module-images', 's3');
            // optimizeImage(public_path() . '/uploads/' . $changesArray['thumbnail_image_path'], 1000);
        }

        $module->update($changesArray);

        return redirect('groups/'. $group->slug . '/sequence/modules/'.$module->id);
    }

    public function deleteModule(Group $group, Module $module, Request $request)
    {
        $module->delete();

        return redirect('groups/'. $group->slug . '/sequence');
    }

    public function markModuleCompleted(Group $group, Module $module, Request $request)
    {
        ModuleUser::where([
            'user_id' => $request->user()->id,
            'module_id' => $module->id,
        ])->update([
            'completed_at' => \DB::raw('NOW()'),
        ]);

        $modules = $group->sequence->modules;
        $hasUserCompletedAllModules = true;
        foreach($modules as $module)
            if(!$module->hasUserCompleted($request->user()))
                $hasUserCompletedAllModules = false;

        SequenceUser::where([
            'user_id' => $request->user()->id,
            'sequence_id' => $group->sequence->id,
        ])->update([
            'last_completed_module_id' => $module->id,
            'completed_at' => ($hasUserCompletedAllModules) ? \DB::raw('NOW()') : null,
        ]);

        if ($hasUserCompletedAllModules) {
            if ($group->sequence->is_completion_shoutouts_enabled)
                $this->createShoutoutForSequenceCompletion($group, $request->user());
            if ($group->sequence->completed_badge_id)
                $this->awardSequenceCompletedBadgeToUser($group->sequence, $request->user());
        }

        return redirect('/groups/'.$group->slug.'/sequence');
    }

    protected function createShoutoutForSequenceCompletion(Group $group, $user)
    {
        if(Shoutout::where('sequence_group_id', $group->id)->where('shoutout_to', $user->id)->exists())
            return;
        
        $shoutout = Shoutout::create([
            'shoutout_by' => null,
            'shoutout_to' => $user->id,
            'body' => 'for completing ' . $group->sequence->name . '!',
            'sequence_group_id' => $group->id,
        ]);
        
        $post = Post::create([
            'post_type' => get_class($shoutout),
            'post_id' => $shoutout->id,
            'group_id' => $group->id,
            'posted_as_group_id' => $group->id,
        ]);
        $post->groups()->attach($group->id);

        event(new ShoutoutMade(false, $post));
    }

    protected function awardSequenceCompletedBadgeToUser($sequence, $user)
    {
        $user->options()->syncWithoutDetaching($sequence->completed_badge_id);

        $sequence->completedBadge->touch();
        $user->touch();
    }

    public function markModuleIncomplete(Group $group, Module $module, Request $request)
    {
        //arguably the most pointless feature on the site

        ModuleUser::where([
            'user_id' => $request->user()->id,
            'module_id' => $module->id,
        ])->update([
            'completed_at' => null,
        ]);

        SequenceUser::where([
            'user_id' => $request->user()->id,
            'sequence_id' => $group->sequence->id,
        ])->update([
            'last_completed_module_id' => $module->id,
            'completed_at' => null,
        ]);

        //because I want to know how pointless this is
        $request->user()->logs()->create([
            'action' => 'Module',
            'message' => 'Marked module as incomplete',
        ]);

        return redirect('/groups/'.$group->slug.'/sequence');
    }

}
