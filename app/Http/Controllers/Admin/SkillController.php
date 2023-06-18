<?php

namespace App\Http\Controllers\Admin;

use App\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SkillController extends Controller
{
    public function index()
    {
        $skills = Skill::orderBy('name', 'asc');

        if(isset($_GET['userCreated']) && $_GET['userCreated'] == 'true')
            $skills->whereNotNull('created_by');

        return view('admin.categories.skills.index')->with([
            'skills' => $skills->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.skills.create')->with([
            'parents' => \DB::table('skills')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required',
        ]);

        Skill::create([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/skills');
    }

    public function edit($id, Request $request)
    {
        return view('admin.categories.skills.edit')->with([
            'skill' => Skill::find($id),
            'parents' => \DB::table('skills')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required'
        ]);
        
        Skill::where('id', '=', $id)->update([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/skills');
    }

    public function delete($id, Request $request)
    {
        $skill = Skill::find($id);
        $skill->delete();

        return redirect('/admin/categories/skills');
    }

}
