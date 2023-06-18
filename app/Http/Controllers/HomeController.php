<?php

namespace App\Http\Controllers;

use Mail;
use App\User;
use App\Post;
use App\Setting;
use App\VirtualRoom;
use App\ArticlePost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['logo', 'contactUs', 'contact']);
        $this->middleware('onboarding')->except(['logo', 'contactUs', 'contact']);
        $this->middleware('event.only', ['only' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('spa');
        $posts = $request->user()->dashboard_posts;
 
        $twoWeekOldArticles = Post::userPosts()
                                 ->whereHasMorph('post', [\App\ArticlePost::class])
                                 ->where('post_type', '=', 'App\ArticlePost')
                                 ->orderBy('posts.post_at', 'desc')
                                 ->limit(6)
                                 ->simplePaginate(10);

        $header_image = getSetting('dashboard_header_image');

        $agent = new \Jenssegers\Agent\Agent;

        if(getsetting('is_dashboard_virtual_room_enabled'))
        {
            $desktopRoom = VirtualRoom::find(getSetting('dashboard_virtual_room_id'));
            $mobileRoom = VirtualRoom::find(getSetting('mobile_dashboard_virtual_room_id'));

            if($desktopRoom && $desktopRoom->image_path && ($agent->isDesktop() || !$mobileRoom))
                $virtualRoom = $desktopRoom;
            else if($mobileRoom && $mobileRoom->image_path && $agent->isMobile())
                $virtualRoom = $mobileRoom;
            else
                $virtualRoom = false;
        }
        else
            $virtualRoom = false;

        return view('home')->with([
            'posts' => $posts,
            'twoWeekOldArticles' => $twoWeekOldArticles,
            'header_image' => $header_image,
            'dashboard_left_nav_image' => getSetting('dashboard_left_nav_image'),
            'dashboard_left_nav_image_link' => getSetting('dashboard_left_nav_image_link'),
            'does_dashboard_left_nav_image_open_new_tab' => getSetting('does_dashboard_left_nav_image_open_new_tab'),
            'virtualRoom' => $virtualRoom,
        ]);
    }

    public function getArticle($id)
    {
        $post = ArticlePost::find($id);
        $post->clicks = $post->clicks + 1;
        $post->save();

        auth()->user()->logs()->create([
            'action'             => 'read article',
            'related_model_type' => get_class($post),
            'related_model_id'   => $post->id,
        ]);

        if ($post->code == null)
            return redirect($post->url);

        return view('content.show')->with([
            'post' => $post,
        ]);
    }

    public function contactUs(Request $request)
    {
        return view('support.contactUs')->with([
            'user' => $request->user(),
            'confirm' => $request->confirm,
        ]);
    }

    public function contact(Request $request)
    {
        $admins = User::admins()->pluck('email');
        Mail::to($admins->first())->cc($admins->skip(1))->send(new \App\Mail\Support($request->all()));

        return redirect('/contact-us?confirm=true');
    }

    public function LogLeadRedirect(Request $request)
    {
        $request->user()->logs()->create([
            'action' => 'lead-gen',
            'message' => 'Clicked Powered By OTD link',
        ]);

        return redirect($request->next);
    }

    public function privacyPolicy() {
        return view('privacy-poliicy');
    }

   

}
