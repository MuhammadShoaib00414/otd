<?php

namespace App\Http\Controllers\Group;

use DB;
use App\Post;
use App\Group;
use App\Event;
use App\File;
use App\ArticlePost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index($slug)
    {
    	$group = Group::where('slug', $slug)->first();
    	$clicks = DB::table('logs')->selectRaw('action, count(created_at) as count, count(distinct(user_id)) as userCount')
    							  ->groupBy('action')
                                  ->where('related_model_type', 'App\Group')
    							  ->where('related_model_id', $group->id)
                                  ->orderBy('userCount', 'desc')->get();

    	return view('groups.activity.index')->with([
    		'group' => $group,
    		'clicks' => $clicks,
    	]);
    }

    public function show($slug, $action)
    {
    	$action = str_replace('-', ' ', $action);
    	$group = Group::where('slug', $slug)->first();
    	$clicks = $group->logs()->where('action', $action)->get();

    	if($action == 'viewed event')
    	{
    		$clicks = $clicks->groupBy('secondary_related_model_id')->filter(function ($clicks, $eventId) {
                return Event::where('id', $eventId)->exists();
            })->map(function ($clicks, $eventId) {
    			$event = Event::find($eventId);
    			$event->count = $clicks->unique('user_id')->count();
    			$users = [];
    			foreach($clicks as $click)
    			{
    				$users[$click->user->name] = [
                        'rsvp' => $event->rsvpFor($click->user->id), 
                        'count' => $clicks->where('user_id', $click->user_id)->count(),
                    ];
    			}

    			$event->users_clicked = $users;

    			return $event;
    		});
    	}
        else if($action == 'clicked content')
        {
            $clicks = $clicks->whereNotNull('secondary_related_model_id')->groupBy('secondary_related_model_id')->map(function ($clicks, $articleId) {
                $article = ArticlePost::find($articleId);
                if($article)
                {
                    $article->count = $clicks->unique('user_id')->count();
                    $users = [];
                    foreach($clicks as $click)
                    {
                        $users[$click->user->name] = [
                            'count' => $clicks->where('user_id', $click->user_id)->count(),
                        ];
                    }
                    $article->users_clicked = $users;

                    return $article;
                }
            });
        }
        else if($action == 'clicked subgroup')
        {
            $clicks = $clicks->whereNotNull('secondary_related_model_id')->groupBy('secondary_related_model_id')->map(function ($clicks, $groupId) {
                $subgroup = Group::find($groupId);
                if($subgroup)
                {
                    $subgroup->count = $clicks->unique('user_id')->count();
                    $users = [];
                    foreach($clicks as $click)
                    {
                        $users[$click->user->name] = [
                            'count' => $clicks->where('user_id', $click->user_id)->count(),
                        ];
                    }
                    $subgroup->users_clicked = $users;

                    return $subgroup;
                }
            });
        }
        else if($action == 'clicked post link')
        {
           $clicks = $clicks->whereNotNull('secondary_related_model_id')->groupBy('secondary_related_model_id')->map(function ($clicks, $groupId) {
                $post = Post::find($groupId);
                if($post)
                {
                    $post->count = $clicks->unique('user_id')->count();
                    $users = [];
                    foreach($clicks as $click)
                    {
                        $users[$click->user->name] = [
                            'count' => $clicks->where('user_id', $click->user_id)->count(),
                        ];
                    }
                    $post->users_clicked = $users;

                    return $post;
                }
            }); 
        }
        else if($action == 'downloaded file')
        {
            $clicks = $clicks->whereNotNull('secondary_related_model_id')->groupBy('secondary_related_model_id')->map(function ($clicks, $groupId) {
                $file = File::find($groupId);
                if($file)
                {
                    $file->count = $clicks->unique('user_id')->count();
                    $users = [];
                    foreach($clicks as $click)
                    {
                        $users[$click->user->name] = [
                            'count' => $clicks->where('user_id', $click->user_id)->count(),
                        ];
                    }
                    $file->users_clicked = $users;

                    return $file;
                }
            }); 
        }
    	else
    		$clicks = $clicks->groupBy('user_id');
    	
    	$clicks = $clicks->sortByDesc(function ($logs, $id) {
            if($logs)
    		  return $logs->count();
            else
                return 0;
    	});

    	return view('groups.activity.show')->with([
    		'group' => $group,
    		'groupedClicks' => $clicks,
    		'action' => $action,
    	]);
    }
}
