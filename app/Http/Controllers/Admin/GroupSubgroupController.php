<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GroupSubgroupController extends Controller
{
    public function index($group)
    {
        $group = Group::withTrashed()->find($group);
        
        return view('admin.groups.subgroups.index')->with([
            'group' => $group,
            'subgroups' => $group->subgroups()->orderBy('order_key', 'asc')->get(),
        ]);
    }

    public function sort(Request $request)
    {
    	$count = 1;
    	foreach($request->subgroups as $subgroupId)
    	{
    		Group::where('id', $subgroupId)->update(['order_key' => $count]);
    		$count++;
    	}

    	return response(200);
    }
}
