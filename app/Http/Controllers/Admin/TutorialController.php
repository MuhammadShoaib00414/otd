<?php

namespace App\Http\Controllers\Admin;

use App\Tutorial;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TutorialController extends Controller
{
    public function index()
    {
    	return view('admin.tutorials.index')->with([
    		'tutorials' => Tutorial::orderBy('id', 'desc')->get(),
    	]);
    }

    public function edit($tutorialId)
    {
    	return view('admin.tutorials.edit')->with([
    		'tutorial' => Tutorial::find($tutorialId),
    	]);
    }

    public function update($tutorialId, Request $request)
    {
    	Tutorial::find($tutorialId)->update([
    		'url' => $request->url,
    	]);

    	return redirect('/admin/tutorials');
    }
}
