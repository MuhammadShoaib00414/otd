<?php

namespace App\Http\Controllers\Admin;

use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class TermsConditionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.terms-and-conditions.terms-conditions')->with([
            'is_terms_and_conditions' => Setting::where('name','is_terms_and_conditions')->first(),
        
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $is_conditions = Setting::find($id);

        $is_conditions->update([
            'value' => $request->content
        ]);
       
        $is_conditions = Setting::find($id);
        // Getting values from the blade template form
        $is_conditions->value =  $request->content;
      
        $is_conditions->save();
      
        Session::put('success', "Successfully updated!");
        
        return redirect('/admin/terms-and-conditions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function termsOfCondition() {
        return view('terms-of-service')->with([
            'tesrm_and_conditions' => Setting::where('name','is_terms_and_conditions')->first(),
        ]);
        
    }
}
