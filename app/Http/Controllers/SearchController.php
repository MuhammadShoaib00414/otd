<?php

namespace App\Http\Controllers;

use App\User;
use App\Event;
use App\Group;
use App\Ideation;
use App\ArticlePost;
use App\DiscussionThread;
use Illuminate\Http\Request;
use Spatie\Searchable\Search;
use Illuminate\Support\Facades\Cache;
class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		Cache::flush();
    }

    public function search(Request $request)
    {
    	$user = $request->user();
    	$should_restrict = !$user->is_admin;
    	if($request->has('q') && $request->q != '') {
    		$searchResults = (new Search())
    		->registerModel(DiscussionThread::class, function($modelSearchAspect) use ($should_restrict, $user) {
		   		$modelSearchAspect->addSearchableAttribute('name')->whereHas('group')->orderBy('name');
		   		if($should_restrict)
		   		{
		   			$groups = $user->groups;
		   			$discussionIds = collect([]);
		   			foreach($groups as $group)
		   				$discussionIds = $discussionIds->merge($group->discussions()->pluck('id'));
		   			$modelSearchAspect->whereIn('id', $discussionIds);
		   		}
		   	})
    		->registerModel(ArticlePost::class, function($modelSearchAspect) use ($should_restrict, $user) {
		   		$modelSearchAspect->addSearchableAttribute('title');
		   		if($should_restrict)
		   		{
		   			$modelSearchAspect->whereHas('listing', function($query) use ($user) {
		   				return $query->whereHas('groups', function ($query) use ($user) {
		   					return $query->whereIn('groups.id', $user->groups()->pluck('id'));
		   				});
		   			});
		   		}
		   	})
		   	->registerModel(Event::class, function($modelSearchAspect) use ($should_restrict, $user) {
		   		$modelSearchAspect->addSearchableAttribute('name')->orderBy('name');
		   		$modelSearchAspect->where(function($query) {
		   			return $query->whereHas('group')
		   						->orWhereHas('groups');
		   		});
		   		if($should_restrict)
		   			$modelSearchAspect->whereIn('id', $user->all_events->pluck('id'));
		   	})
		   	->registerModel(Ideation::class, function($modelSearchAspect) use ($should_restrict, $user, $request) {
		   		$modelSearchAspect->addSearchableAttribute('name')->orderBy('name');
		   		if($should_restrict)
		   			$modelSearchAspect->whereIn('id', $user->ideations()->pluck('id'));
		   	})
		   	->registerModel(Group::class, function($modelSearchAspect) use ($should_restrict, $user) {
		   		$modelSearchAspect->addSearchableAttribute('name');
		   		if($should_restrict)
		   			$modelSearchAspect->whereIn('id', $user->groups()->pluck('id'));
		   	})
		   	->registerModel(User::class, function($modelSearchAspect) use ($should_restrict, $user) {
		   		$modelSearchAspect->addSearchableAttribute('name');
		   		$modelSearchAspect->addSearchableAttribute('job_title');
		   		$modelSearchAspect->addSearchableAttribute('company');
		   		$modelSearchAspect->addSearchableAttribute('location');
		   		$modelSearchAspect->addSearchableAttribute('search')->orderBy('name');
                $modelSearchAspect->where('is_hidden', '=', 0);
                $modelSearchAspect->where('is_enabled', '=', 1);
		   		if($should_restrict) {
		   			$groups = $user->groups;
		   			$userIds = collect([]);
		   			foreach($groups as $group)
		   				$userIds = $userIds->merge($group->users()->pluck('id'));
		   			$modelSearchAspect->whereIn('id', $userIds);
		   		}
				
				$blockedUsersIds = get_blocked_users_ids();
				$modelSearchAspect->whereNotIn('id', $blockedUsersIds);
		   	})
		   ->search($request->q);
    	}
    	else
    	{
    		$searchResults = collect([]);
    	}
	
		return view('search.index')->with([
			'query' => $request->has('q') ? $request->q : '',
			'results' => $searchResults,
		]);
    }
}
