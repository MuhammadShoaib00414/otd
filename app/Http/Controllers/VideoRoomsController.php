<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VideoRoomsController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }
    
    public function show($slug, Request $request)
    {
        return view('videorooms.show')->with([
            'slug' => $slug,
        ]);
    }
}
