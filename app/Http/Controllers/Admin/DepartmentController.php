<?php

namespace App\Http\Controllers\Admin;

use App\Department;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('admin.categories.departments.index')->with([
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.departments.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Department::create([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/departments');
    }

    public function edit($id, Request $request)
    {
        return view('admin.categories.departments.edit')->with([
            'department' => Department::find($id),
        ]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Department::where('id', '=', $id)->update([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/departments');
    }

    public function delete($id, Request $request)
    {
        $category = Department::find($id);
        $category->delete();

        return redirect('/admin/categories/departments');
    }

}
