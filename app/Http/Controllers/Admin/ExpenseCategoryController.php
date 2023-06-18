<?php

namespace App\Http\Controllers\Admin;

use App\ExpenseCategory as Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.expense.index')->with([
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.expense.create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Category::create([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/expense-categories');
    }

    public function edit($id, Request $request)
    {
        return view('admin.categories.expense.edit')->with([
            'category' => Category::find($id),
        ]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
        ]);

        Category::where('id', '=', $id)->update([
            'name' => $request->input('name'),
        ]);

        return redirect('/admin/categories/expense-categories');
    }

    public function delete($id, Request $request)
    {
        $category = Category::find($id);
        $category->delete();

        return redirect('/admin/categories/expense-categories');
    }

}
