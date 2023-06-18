<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\Http\Controllers\Controller;
use App\Option;
use App\Taxonomy;
use App\User;
use Illuminate\Http\Request;

class TaxonomiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxonomy = Taxonomy::first();

        return redirect('/admin/categories/' . $taxonomy->id);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function
    store(Request $request)
    {
        $taxonomy = Taxonomy::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_enabled' => $request->has('is_enabled') ? 1 : 0,
            'is_public' => $request->has('is_public') ? 1 : 0,
            'is_user_editable' => $request->has('is_user_editable') ? 1 : 0,
            'is_badge' => $request->has('is_badge') ? 1 : 0,
            'is_visible_in_group_admin_reporting' => $request->has('is_visible_in_group_admin_reporting') ? 1 : 0,
            'localization' => $request->localization,
            'is_customer_option' => $request->has('is_customer_option') ? 1 : 0,
        ]);

        return redirect('/admin/categories/' . $taxonomy->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Taxonomy $taxonomy, Request $request)
    {

        $options = $taxonomy->options()->orderBy('name', 'asc');
        if ($request->has('userCreated') && $request->userCreated == "true")
            $options->whereNotNull('created_by');

        return view('admin.categories.show')->with([
            'taxonomy' => $taxonomy,
            'options' => $options->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Taxonomy $taxonomy)
    {
        return view('admin.categories.edit')->with([
            'taxonomy' => $taxonomy,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Taxonomy $taxonomy)
    {

        $taxonomy->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_enabled' => $request->has('is_enabled') ? 1 : 0,
            'is_public' => $request->has('is_public') ? 1 : 0,
            'is_user_editable' => $request->has('is_user_editable') ? 1 : 0,
            'is_visible_in_group_admin_reporting' => $request->has('is_visible_in_group_admin_reporting') ? 1 : 0,
            'is_badge' => $request->has('is_badge') ? 1 : 0,
            'localization' => $request->localization,
            'is_customer_option' => $request->has('is_customer_option') ? 1 : 0,

        ]);

        return redirect('/admin/categories/' . $taxonomy->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Taxonomy $taxonomy)
    {
        $taxonomy->delete();

        return redirect('/admin/categories');
    }

    public function sortTaxonomies(Request $request)
    {
        $sortType = 'profile';
        if ($request->has('sort'))
            $sortType = $request->sort;

        return view('admin.categories.sort')->with([
            'taxonomies' => Taxonomy::where('is_enabled', 1)->orderBy($sortType . '_order_key')->get(),
            'sortType' => $sortType,
        ]);
    }

    public function updateSortTaxonomies(Request $request)
    {
        $count = 1;
        foreach ($request->taxonomies as $taxonomyId) {
            Taxonomy::where('id', $taxonomyId)->update([
                $request->orderType . '_order_key' => $count,
            ]);
            $count++;
        }

        return response(200);
    }

    public function sort(Taxonomy $taxonomy, Request $request)
    {
        $sortType = 'profile';
        if ($request->has('sort')) {
            $sortType = $request->sort;
        }

        $groupedOptions = $taxonomy->groupedOptionsWithOrderKey($sortType, false);

        return view('admin.categories.options.sort', [
            'taxonomy' => $taxonomy,
            'groupedOptions' => $groupedOptions,
            'sortType' => $sortType,
        ]);
    }


    public function custom_group_sort(Taxonomy $taxonomy, Request $request)
    {

        $sortType = 'parent';
        if ($request->has('sort'))
            $sortType = $request->sort;

        $selection = ['*'];
        $groupedOptions = $taxonomy->groupedOptionsWithOrderKeyParent($sortType, false);

        

        return view('admin.categories.options.custom-group-sort')->with([
            'taxonomy' => $taxonomy,
            'groupedOptions' => $groupedOptions,
            'sortType' => $sortType,
        ]);
    }

    public function updateSort(Taxonomy $taxonomy, Request $request)
    {

        $parentCount = 1;

        foreach (($request->groupedOptions) as $parent => $options) {

            $optionCount = 1;
            foreach ($options as $optionId) {

                $option = Option::find($optionId);

                Option::where('id', $optionId)->update([
                    $request->orderType . '_order_key' => $parentCount . '-' . $optionCount,
                    'parent' => $parent == 'empty' ? '' : $parent,
                ]);
                $optionCount++;
            }
            $parentCount++;
        }

        return response(200);
    }

    public function updateSortcustomGroup(Taxonomy $taxonomy, Request $request)
    {
        $parentCount = 1;

        foreach (($request->groupedOptions) as $parent => $options) {

            $optionCount = 1;
            foreach ($options as $optionId) {

                $option = Option::find($optionId);

                Option::where('id', $optionId)->update([
                    $request->orderType . '_order_key' => $parentCount . '-' . $optionCount,
                    // 'parent' => $parent == 'empty' ? '' : $parent,
                ]);
                $optionCount++;
            }
            $parentCount++;
        }

        return response(200);
    }

    public function alphabetizecustomgroup(Taxonomy $taxonomy, Request $request)
    {
        $opt = Option::orderBy('name')->pluck('id');

        $i = 1;
        foreach ($request->groupedOptions as $key => $value) {
            Option::where('parent', $key)->update([
                'parent_order_key' => $i
            ]);
            $i++;
        }

        foreach ($opt as $optionId) {
            $option = Option::find($optionId);
            // Display the option in alphabetical order here, e.g.:
            echo $option->name;
        }

        return response(200);
    }


    public function alphabetize(Taxonomy $taxonomy, Request $request)
    {

        $parentCount = 1;

        foreach ($taxonomy->grouped_options->sort() as $parent => $options) {
            $optionCount = 1;

            foreach ($options->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE) as $option) {

                $option->update([
                    $request->orderType . '_order_key' => $parentCount . '-' . $optionCount,
                    // 'parent' => $parent == 'empty' ? '' : $parent,
                ]);
                $optionCount++;
            }
            $parentCount++;
        }

        return response(200);
    }

    public function copySort(Taxonomy $taxonomy, Request $request)
    {
        foreach ($taxonomy->options as $option) {
            $option[$request->to . '_order_key'] = $option[$request->from . '_order_key'];
            $option->save();
        }

        return response(200);
    }

    public function addUsers(Taxonomy $taxonomy, Request $request)
    {
        $users = User::visible()->orderBy('name', 'asc')->get();
        $groups = Group::whereNull('parent_group_id')->with('subgroups')->orderBy('name', 'asc')->get();

        return view('admin.categories.addusers')->with([
            'taxonomy' => $taxonomy,
            'users' => $users,
            'groups' => $groups,
        ]);
    }

    public function postAddUsers(Taxonomy $taxonomy, Request $request)
    {
        $option = Option::find($request->option_id);

        if (!$option)
            return back()->with('error', 'You must select a valid option.');

        $count = 0;

        if ($request->has('users'))
            $count += count($option->users()->syncWithoutDetaching($request->users)['attached']);

        if ($request->has('groups')) {
            foreach ($request->groups as $groupId) {
                $group = Group::find($groupId);
                $count += count($option->users()->syncWithoutDetaching($group->users()->pluck('id'))['attached']);
            }
        }

        $option->touch();

        return redirect('/admin/categories/' . $taxonomy->id . '/add-users')->with('success', 'Success: ' . $count . ' total users added to ' . $option->name . '. ' . ((($request->has('users') && count($request->users)) - $count > 0) ? (count($request->users) - $count > 0) . ' users already associated with ' . $option->name : ''));
    }

    public function editGroupings(Taxonomy $taxonomy)
    {
        $groupings = $taxonomy->options()->whereNotIn('parent', ['User Created'])->pluck('parent')->unique()->merge($taxonomy->options()->whereNull('parent')->pluck('parent')->unique());

        //because laravel doesn't like empty vs null cols
        $groupings = $groupings->map(function ($grouping, $key) use ($taxonomy) {
            $localization = $taxonomy->options()->where('parent', $grouping)->first()->localization;
            if ($grouping == '' or is_null($grouping))
                return ['value' => '', 'localization' => $localization];
            else {
                return ['value' => $grouping, 'localization' => $localization];
            }
        })->unique();


        return view('admin.categories.groupings.bulkEdit')->with([
            'taxonomy' => $taxonomy,
            'groupings' => $groupings,
        ]);
    }

    public function updateGroupings(Taxonomy $taxonomy, Request $request)
    {
        $is_localization_enabled = getsetting('is_localization_enabled');
        foreach ($request->groupings as $oldGrouping => $newGrouping) {
            $query = $taxonomy->options();
            if ($oldGrouping == 'Empty Grouping') {
                $query = $query->whereNull('options.parent')->orWhere('options.parent', '=', '');
            } else
                $query = $query->where('options.parent', '' . $oldGrouping);

            //this next section of code is to avoid overriding the localized name of each option. Getting every option and looping through them isn't ideal, but as far as davis knows thats the only way.

            foreach ($query->get() as $option) {
                if ($is_localization_enabled) {
                    $newLocalization = ['es' =>
                    ['parent' => $request->has("localization") && array_key_exists($oldGrouping, $request->localization) ? $request->localization[$oldGrouping] : '']];
                    $localization = $option->localization;
                    if ($localization && array_key_exists('es', $localization) && array_key_exists('name', $localization['es']))
                        $newLocalization['es']['name'] = $localization['es']['name'];
                } else
                    $newLocalization = json_encode(json_decode('{}'));

                $query->update([
                    'parent' => $newGrouping,
                    'localization' => $newLocalization,
                ]);
            }
        }

        return redirect('/admin/categories/' . $taxonomy->id);
    }
}
