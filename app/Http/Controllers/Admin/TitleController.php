<?php

namespace App\Http\Controllers\Admin;

use App\Title;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TitleController extends Controller
{
    public function index()
    {
        return view('admin.categories.titles.index')->with([
            'titles' => Title::all(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.titles.create');
    }
    
    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Title::create([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/titles');
    }
    
    public function edit($id, Request $request)
    {
        return view('admin.categories.titles.edit')->with([
            'title' => Title::find($id),
        ]);
    }
    
    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Title::where('id', '=', $id)->update([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/titles');
    }

    public function delete($id, Request $request)
    {
        $category = Title::find($id);
        $category->delete();

        return redirect('/admin/categories/titles');
    }
    
}
