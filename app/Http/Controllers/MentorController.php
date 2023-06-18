<?php

namespace App\Http\Controllers;

use App\Category;
use App\Keyword;
use App\Skill;
use App\Taxonomy;
use Illuminate\Http\Request;

class MentorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function ask(Request $request)
    {
        $request->user()->logs()->create([
            'action' => 'used Ask a Mentor',
        ]);

        $taxonomies = Taxonomy::where('is_enabled', 1)->orderBy('mentor_order_key')->get();

        $data = [];
        foreach($taxonomies as $taxonomy){ 
            if ($taxonomy->ordered_grouped_options_with_mentors->count()) {
                $data[] = (Object) [
                    'name' => $taxonomy->name,
                    'display' => false,
                    'options' => $taxonomy->ordered_grouped_options_with_mentors,
                ];
            }
        }

        return view('mentors.ask')->with([
            'taxonomies' => json_encode($data),
        ]);
    }
}
