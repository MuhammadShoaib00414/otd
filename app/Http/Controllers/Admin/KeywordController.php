<?php

namespace App\Http\Controllers\Admin;

use App\Keyword;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KeywordController extends Controller
{
    public function index()
    {
        $keywords = Keyword::orderBy('name', 'asc');

        if(isset($_GET['userCreated']) && $_GET['userCreated'] == 'true')
            $keywords->whereNotNull('created_by');

        return view('admin.categories.keywords.index')->with([
            'keywords' => $keywords->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.keywords.create')->with([
            'parents' => \DB::table('keywords')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required'
        ]);

        Keyword::create([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/keywords');
    }

    public function edit($id, Request $request)
    {
        return view('admin.categories.keywords.edit')->with([
            'keyword' => Keyword::find($id),
            'parents' => \DB::table('keywords')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required'
        ]);
        
        Keyword::where('id', '=', $id)->update([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/keywords');
    }

    public function delete($id, Request $request)
    {
        $category = Keyword::find($id);
        $category->delete();

        return redirect('/admin/categories/keywords');
    }

}
