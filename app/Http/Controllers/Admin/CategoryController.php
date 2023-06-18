<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use App\Http\Controllers\Controller;
use App\Keyword;
use App\Option;
use App\Skill;
use App\Taxonomy;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc');
        if(isset($_GET['userCreated']) && $_GET['userCreated'] == "true")
            $categories->whereNotNull('created_by');

        return view('admin.categories.categories.index')->with([
            'categories' => $categories->get(),
        ]);
    }

    public function create()
    {
        return view('admin.categories.categories.create')->with([
            'parents' => \DB::table('categories')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required',
        ]);

        Category::create([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/categories');
    }

    public function edit($id, Request $request)
    {
        return view('admin.categories.categories.edit')->with([
            'category' => Category::find($id),
            'parents' => \DB::table('categories')->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get(),
        ]);
    }

    public function update($id, Request $request)
    {
        $validate = $request->validate([
            'name' => 'required',
            'parent' => 'required',
        ]);

        Category::where('id', '=', $id)->update([
            'name' => $request->input('name'),
            'parent' => $request->input('parent'),
        ]);

        return redirect('/admin/categories/categories');
    }

    public function delete($id, Request $request)
    {
        $category = Category::find($id);
        $category->delete();

        return redirect('/admin/categories/categories');
    }

    public function approval()
    {
        $queue = Option::orderBy('name')->where('is_enabled', 0)->whereNotNull('created_by')->get();

        return view('admin.categories.approval')->with([
            'queue' => $queue,
        ]);
    }

    public function approve(Request $request)
    {
        $item = Option::find($request->id);

        if($request->action == 'approve')
            $item->update(['is_enabled' => 1]);
        else
            $item->delete();

        return redirect('/admin/categories/approval');
    }

    public function indexMergables(Request $request)
    {
        $options;
        $taxonomy = Taxonomy::find($request->taxonomy);
        $parents = $taxonomy->options()->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get();

        if($request->has('sort') && !empty($request->sort))
        {
            if($request->sort == 'members')
                $options = $taxonomy->options()->withCount('users')->orderByDesc('users_count');
            else
                $options = $taxonomy->options()->orderBy($request->sort);
        } else {
            $options = $taxonomy->options()->orderBy('name', 'asc');
        }


        return view('admin.categories.merge')->with([
            'taxonomy' => $taxonomy,
            'options' => $options->get(),
            'parents' => $parents,
        ]);
    }

    public function merge(Request $request)
    {
        $optionIds = $request->merge_from;
        $newName = $request->new_name;
        $taxonomy = Taxonomy::find($request->taxonomy);
        $parent = $request->parent;

        $newOption = $taxonomy->options()->create([
            'name' => $newName,
            'parent' => $parent,
        ]);

        $optionIds[] = $newOption->id;

        $this->mergeFromIds($optionIds, $taxonomy);

        $newOption->users->each(function ($user) {
            $user->updateSearch();
        });

        return redirect('/admin/categories/merge?taxonomy='.$taxonomy->id);
    }

    //merges all categories of $type to the last one in the given array
    protected function mergeFromIds(array $ids, $taxonomy)
    {
        for($i = 0; $i < count($ids) - 1; $i++) {
            $from = Option::find($ids[$i]);
            $to = Option::find($ids[$i + 1]);

            $this->mergeTwo($from, $to);
        }
    }

    protected function getCategory(String $type)
    {
        if($type == "skill")
            return \App\Skill::class;
        else if($type == "category")
            return \App\Category::class;
        else
            return \App\Keyword::class;
    }

    //merges $one into $two
    protected function mergeTwo($from, $to)
    {
        $combinedUsers = $from->users->merge($to->users);

        $from->users()->detach($from->users);
        $from->delete();

        $to->users()->sync($combinedUsers);
    }

}
