<?php

namespace App\Http\Controllers;

use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {

        // Execute your cron job command
        $request->validate(['value' => 'required']);
        if($request->parent) {
            $parentsdata =  $request->parent; 
         }else{
            $parentsdata =  'My Activities'; 
         }
		$option = Option::create([
            'taxonomy_id' => $request->taxonomy_id,
			'name' => $request->value,
			'created_by' => $request->user()->id,
            'is_enabled' => 0,
            'parent' =>  $parentsdata,
		]);

        $request->user()->options()->attach($option);
        // Artisan::call('notification:send-pending-approval');
    	return response(['name' => $option->name, 'id' => $option->id]);
    }
}

