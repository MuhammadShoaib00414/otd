<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\Ideation;
use App\ReportedPost;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportedController extends Controller
{
    public function index()
    {
    	$groups = Group::all();

    	$groupsWithReportedPosts = $groups->filter(function($group){
    		return $group->reported_posts->count();
    	});

        $ideations = Ideation::all();

        $ideations = $ideations->filter(function($ideation) {
            return $ideation->reported_count;
        });

    	return view('admin.reported.index')->with([
    		'postables' => $ideations->merge($groupsWithReportedPosts),
    	]);
    }

    public function resolved()
    {
    	$groups = \App\Group::all();

    	$groupsWithResolvedPosts = $groups->filter(function($group){
    		return $group->resolved_posts->count();
    	});
        
    	return view('admin.reported.resolved')->with([
    		'groups' => $groupsWithResolvedPosts,
    	]);
    }
}
