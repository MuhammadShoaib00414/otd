<?php

namespace App\Http\Controllers\Admin;

use App\Option;
use App\Taxonomy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\Translators\OptionsTaxonomy;

class OptionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $taxonomy = Taxonomy::find($request->taxonomy);
        $parents = $taxonomy->options()->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get();

        return view('admin.options.create')->with([
            'taxonomy' => $taxonomy,
            'parents' => $parents,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validate = $request->validate([
            'taxonomy_id' => 'required',
            'name' => 'required',
        ]);

        $taxonomy = Taxonomy::find($request->taxonomy_id);

        $icon = null;

        if($taxonomy->is_badge)
        {
            if($request->has('icon'))
                $icon = $request->icon->store('badge-icons', 's3');
            else
                $icon = 'images/badge-default.svg';
        }
        if($request->parent) {
            $parentsdata =  $request->parent; 
         }else{
            $parentsdata =  ''; 
         }

        $taxonomy->options()->create([
            'name' => $request->name,
            'parent' =>  $parentsdata,
            'icon_url' => $icon,
            'localization' => $request->localization,
        ]);
        // $id = $taxonomy->id;
              
        // dispatch the job with the ID as a parameter
        // dispatch(new OptionsTaxonomy($id));

        return redirect('/admin/categories/'.$taxonomy->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Option $option)
    {
        $parents = $option->taxonomy->options()->select('parent')->groupBy('parent')->whereNotNull('parent')->orderBy('parent', 'asc')->get();

        return view('admin.options.edit')->with([
            'option' => $option,
            'parents' => $parents,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Option $option)
    {
       
        $validate = $request->validate([
            'name' => 'required',
        ]);

        if($option->taxonomy->is_badge)
        {
            if($request->has('use_default_icon'))
                $option->update(['icon_url' => 'images/badge-default.svg']);
            else if($request->has('icon'))
                $option->update(['icon_url' => $request->icon->store('badge-icons', 's3')]);
        }

        $localization = $request->localization;
        if($option->localization && array_key_exists('es', $option->localization) && array_key_exists('parent', $option->localization['es']))
            $localization['es']['parent'] = $option->localization['es']['parent'];
        
         if($request->parent) {
            $parentsdata =  $request->parent; 
         }else{
            $parentsdata =  ''; 
         }
          
        $option->update([
            'name' => $request->input('name'),
            'parent' => $parentsdata,
            'localization' => $localization,
        ]);

        // $id = $option->id;
              
        // dispatch the job with the ID as a parameter
        // dispatch(new OptionsTaxonomy($id));
    
        return redirect('/admin/categories/'.$option->taxonomy->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Option $option)
    {
        $taxonomy = $option->taxonomy;
        $option->delete();

        return redirect('/admin/categories/'.$taxonomy->id);
    }
}
