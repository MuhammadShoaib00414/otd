<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Group;
use App\GroupPages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    public function search(Request $request)
    {
        $groups = Group::where('name', 'like', "%{$request->q}%")
                    ->orderBy('name', 'desc')
                    ->get();

        return response()->json($groups);
    }

    public function userGroups(Request $request)
    {
        
        
        $groups = $user->groups;
        $groups = $groups->map(function($group) use ($request) {
            $locale = $request->user()->locale;
            if($locale != 'en')
                $group = $group->localize($locale);

            return $group;
        });
        return response()->json($groups);
    }

    public function show($slug, Request $request) {
        $group = Group::where('slug', $slug)
                      ->with('chatRoom')
                      ->withCount(['sequence', 'budgets'])
                      ->first();

        //if the group is private and the user isn't a member
        if($group->is_private && !$request->user()->groups()->where('id', $group->id)->exists())
            return false;

        $group->header_bg_image_url = getS3Url($group->header_bg_image_path);
        $group->random_active_users = $group->activeUsers()->orderBy('name', 'asc')->limit(12)->distinct()->get();
        $group->is_current_user_member = $group->users()->where('user_id', $request->user()->id)->exists();
        $group->has_subgroups = $group->subgroups()->exists();
        $group->upcoming_events = $group->upcoming_events;
        $group->parent = $group->parent_group_id ? $group->parent : false;

        if ($group->is_virtual_room_enabled && $group->virtualRoom && $group->virtualRoom->image_path) {
            $group->desktop_room = $group->virtual_room;
            $group->desktop_room->image_url = getS3Url($group->desktop_room->image_path);
            if($group->mobile_virtual_room && $group->mobile_virtual_room->image_path) {
                $group->mobile_room = $group->mobile_virtual_room;
                $group->mobile_room->image_url = getS3Url($group->mobile_room->image_path);
            } else
                $group->mobile_room = false;
        } else
         $group->desktop_room = false;
       
         $group->banner_cta_url = ($group->banner_cta_url) ? ((strpos($group->banner_cta_url, '/messages/new')) ?: '/messages/new?'. http_build_query($group->banner_cta_users)) : '/messages/new?'. http_build_query($group->banner_cta_users);
  
         if($group->is_sequence_visible_on_group_dashboard && $group->sequence()->exists())
        {
            $group->is_sequence_enabled = $group->is_sequence_enabled;
            $group->sequence = $group->sequence;
            $group->sequence->modules = $group->sequence->modules()->orderBy('order_key', 'asc')->get();
            foreach($group->sequence->modules as &$module)
            {
                $module->has_current_user_completed = $module->hasUserCompleted($request->user());
            }

            $nextShouldBeUnavailable = false;
            $user = $request->user();
            $lastAvailableIndex = 0;
            foreach ($group->sequence->modules as $key => $module) {
                if($module->hasUserCompleted($user) && !$nextShouldBeUnavailable)
                {
                    $module->is_available = true;
                    $lastAvailableIndex++;
                }
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
            $group->sequence->last_available_index = $lastAvailableIndex;
        } else {
            $modules = [];
            $sequenceUser = optional(null);
        }

        return $group;
    }

    public function getReportedPosts($slug, Request $request) {
        $group = Group::where('slug', $slug)->first();

        return response()->json($group->reported_posts);
    }


    public function getJoinableGroups(Request $request)
    {
        $groups = Group::where('is_private', 0)
                ->where('is_joinable', 1)
                ->whereNull('parent_group_id')
                ->orderBy('name', 'asc')
                ->get();
        foreach ($groups as $key => $group) {
           $groups[$key]['group_member'] = $group->isUserMember(\Auth::user()->id);
        }
        $groups = $groups->map(function($group) {
            if($group->thumbnail_image_path)
                    $group->thumbnail = $group->thumbnail_image_url;
             return $group;
            });
        return $groups;
    }


    public function join($slug, Request $request)
    {
        $group = Group::where('slug', $slug)->first();
        $group->users()->syncWithoutDetaching($request->user()->id);

        return true;
    }
}
