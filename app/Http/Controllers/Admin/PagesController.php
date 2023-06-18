<?php

namespace App\Http\Controllers\Admin;

use App\Group;
use App\GroupPages;
use App\WelcomeEmail;
use App\Helpers\EmailHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PagesController extends Controller
{


    protected $helper;

    public function __construct(EmailHelper $helper)
    {
        $this->helper = $helper;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
 
        return view('admin.pages.index')->with([
            'pages' => GroupPages::orderBy('created_at', 'desc')->with(['user'])->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $groups = Group::get();
        $page_id = Group::latest()->first()->id;
       
        return view('admin.pages.create')->with([
            'groups' => $groups,
            'page_id' => $page_id+1,
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

     
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
        ]);
       if($request->draft == 'Save as draft'){
             $status = '2';
       }else{
             $status =  $request->input('is_active');
       }
        $pages = GroupPages::create([
            'user_id' => Auth::user()->id,
            'title' => $request->input('title'),
            'content' => linkify($request->input('content')),
            'slug' => $request->input('slug'),
            'status' => $request->input('status'),
            'visibility' => $request->input('visibility'),
            'is_active' =>  $status,
            'displayed_show' => $request->input('displayed_show'),
            'show_in_groups' => json_encode($request->input('groups')),
           
        ]);



        Session::put('success', "Successfully created!");

        return redirect('/admin/pages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
       
        return view('admin.pages.show')->with([
            'page' => GroupPages::find($id),
           
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {  
        $page_id = Group::latest()->first()->id;
        $groups = getShareableGroups($id);
       
        return view('admin.pages.edit')->with([
            'groups' => $groups,
            'page' => GroupPages::find($id),
            'page_id' => $page_id+1,
        ]);

        
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

        $pages = GroupPages::find($id);

        if($request->draft == 'Save as draft'){
            $status = '2';
      }else if($request->published && $request->input('status') == '1'){
            $status =  '1';
      }else{
          $status =  $request->input('is_active');
      }

        $pages->update([
            'user_id' => Auth::user()->id,
            'title' => $request->input('title'),
            'content' => linkify($request->input('content')),
            'slug' => $request->input('slug'),
            'status' => $request->input('status'),
            'visibility' => $request->input('visibility'),
            'is_active' =>  $status,
            'displayed_show' => $request->input('displayed_show'),
            'show_in_groups' => json_encode($request->input('groups')),
        ]);

        Session::put('success', "Successfully updated!");

        return redirect('/admin/pages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        GroupPages::find($id)->delete();

        Session::put('success', "Successfully deleted!");


        return redirect('/admin/pages');
    }

    public function getTemplate(Request $request, $id)
    {
        $template = $this->helper->replaceColors(GroupPages::find($id)->content_template);

        $template = $this->helper->replaceYear($template);
        return $template;
    }

    public function getHtml(Request $request, $id)
    {
        $html = $this->helper->replaceYear(GroupPages::find($id)->content);

        return $this->helper->replaceColors($html);
    }

    public function ShowMorePages(Request $request, $slug)
    {
        $is_pages = Setting::where('name', 'is_pages')->first();
        if($is_pages->value == 1) {
            $group = Group::where('slug',$request->group)->first();
            $slug_setting = str_replace('-',' ' ,$slug);
            $settings = Setting::where('value',ucwords($slug_setting))->first();
    
            if($request->group){
                return view('admin.pages.morepages')->with([
                    'pages' => GroupPages::orderBy('created_at', 'desc')->where('is_active',1)->where('displayed_show','on')->whereJsonContains('show_in_groups', "$group->id")->paginate(10),
                    'slug' => $slug,
                    'pageSetting'=>$settings->value
                ]);
            }else{
                return view('admin.pages.morepages')->with([
                    'pages' => GroupPages::orderBy('created_at', 'desc')->with(['user'])->paginate(10),
                    'slug' => $slug,
                    'pageSetting'=>$settings->value
                ]);
            }
        }else{
            return back();
        }
       
       
    }
    public function showPageOnPopup($id)
    {
        $page = GroupPages::where('id',$id)->orderBy('created_at', 'desc')->with(['user'])->first();

        $html = $this->helper->replaceYear($page->content);
        return $this->helper->replaceColors($html);       
    }

 
   
}
