<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Log;
use App\User;
use App\Group;
use App\Event;
use App\ArticlePost;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GroupClicksController extends Controller
{
    public function index($groupId)
    {
    	$group = Group::withTrashed()->find($groupId);
    	$clicks = DB::table('logs')->selectRaw('action, count(created_at) as count')
    							->groupBy('action')->where('related_model_type', 'App\Group')
    							->where('related_model_id', $groupId)->orderBy('count', 'desc')->get();

    	return view('admin.groups.clicks.index')->with([
    		'group' => $group,
    		'clicks' => $clicks,
    	]);
    }

    public function show($groupId, $action)
    {
    	$action = str_replace('-', ' ', $action);
    	$group = Group::withTrashed()->find($groupId);
    	$clicks = $group->logs()->where('action', $action)->get();

    	if($action == 'viewed event')
    	{
    		$clicks = $clicks->groupBy('secondary_related_model_id')->map(function ($clicks, $eventId) {
    			$event = Event::find($eventId);
                if(!$event)
                    return false;
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
    		})->reject(function ($value) {
                return $value === false;
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
    	else
    		$clicks = $clicks->groupBy('user_id');
    	
    	$clicks = $clicks->sortByDesc(function ($logs, $id) {
    		if($logs)
              return $logs->count();
            else
                return 0;
    	});

    	return view('admin.groups.clicks.show')->with([
    		'group' => $group,
    		'groupedClicks' => $clicks,
    		'action' => $action,
    	]);
    }
}
