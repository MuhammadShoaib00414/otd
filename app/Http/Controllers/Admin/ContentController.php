<?php

namespace App\Http\Controllers\Admin;

use App\Log;
use App\Feed;
use App\Post;
use Embed\Embed;
use Carbon\Carbon;
use App\ArticlePost;
use Maatwebsite\Excel\Excel;
use Illuminate\Http\Request;
use App\Exports\ContentExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{

    public function index(Request $request)
    {
        $articles = ArticlePost::query();

        if(!$request->has('sort'))
            $articles = $articles->orderBy('created_at', 'desc');
        else
        {
            if($request->sort == "dateAsc")
                $articles = $articles->orderBy('article_posts.created_at', 'asc');
            else if($request->sort == "dateDesc")
                $articles = $articles->orderBy('article_posts.created_at', 'desc');
            else if($request->sort == "titleAsc")
                $articles = $articles->orderBy('article_posts.title', 'asc');
            else if($request->sort == "titleDesc")
                $articles = $articles->orderBy('article_posts.title', 'desc');
            else if($request->sort == "clicksAsc")
                $articles = $articles->orderBy('article_posts.clicks', 'asc');
            else if($request->sort == "clicksDesc")
                $articles = $articles->orderBy('article_posts.clicks', 'desc');
        }                 

        return view('admin.content.index')->with([
            'articles' => $articles->simplePaginate(25),
        ]);
    }

    public function feeds()
    {
        return view('admin.content.feeds.index')->with([
            'feeds' => Feed::all(),
        ]);
    }

    public function addArticle()
    {
        return view('admin.content.articles.add');
    }

    public function storeArticle(Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required|url',
            'image' => 'url',
        ]);

        try {
            if ($request->has('custom_image_upload') && getimagesize($request->custom_image_upload) == false)
                return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        } catch (\Exception $e) {
            return redirect()->back()->with('invalid-image', 'Error: image url must be an image.');
        }

        if($request->has('custom_image_upload')) {  
            $file = $request->custom_image_upload->store('images', 's3');
            // optimizeImage(public_path() . '/uploads/' . $file, 1800);
        } else {
            $mime = getRemoteMimeType($request->input('image'));
            if(strpos($mime, 'html'))
                return redirect()->back()->withErrors(['Error: URL blocks this file from being used. Please upload a custom image.']);
            try
            {
                $opts = array('http' =>
                    array(
                        'method'  => 'GET',
                        'ignore_errors' => TRUE,
                    )
                );
                $context = stream_context_create($opts);
                $contents = file_get_contents($request->input('image'), false, $context);
            }
            catch(Exception $e)
            {
                return redirect()->back()->with('invalid-image', 'Error: image url is invalid.');
            }
            $mime = explode('/', $mime)[1];
            $file = "images/".time() . ".{$mime}";
            if(strpos($file, '?'))
                $file = substr($file, 0, strpos($file, "?"));
            Storage::disk('s3')->put($file, $contents);
            // optimizeImage(public_path() . '/uploads/' . $name, 1800);
        }
        
        $articlePost = ArticlePost::create([
            'title' => $request->input('title'),
            'image_url' => $file,
            'url' => $request->input('url'),
        ]);

        $post = Post::create([
            'post_type' => get_class($articlePost),
            'post_id' => $articlePost->id,
            'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
        ]);
        $post->groups()->attach($request->input('groups'));

        return redirect('/admin/content');
    }

    public function fetch(Request $request)
    {
        if(!filter_var($request->input('url'), FILTER_VALIDATE_URL))
            return false;
        $page = Embed::create($request->input('url'));

        return [
            'title' => $page->title,
            'image' => $page->image,
            'url' => $page->url,
            'is_video' => str_contains($page->url, 'youtube.com') || str_contains($page->url, 'vimeo.com') || str_contains($page->url, 'facebook.com'),
        ];
    }

    public function createFeed(Request $request)
    {
        return view('admin.content.feeds.create');
    }

    public function storeFeed(Request $request)
    {
        $feed = Feed::create([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'type' => $request->input('type'),
            'status' => 'not yet run',
        ]);

        $feed->groups()->attach($request->input('groups'));

        return redirect('/admin/content/feeds/');
    }

    public function showArticle($id, Request $request)
    {
        $article = ArticlePost::find($id);
        $logs = Log::where('related_model_type', '=', 'App\ArticlePost')
                   ->where('related_model_id', '=', $id)
                   ->orderBy('id', 'desc')
                   ->get();

        return view('admin.content.articles.show')->with([
            'article' => $article,
            'logs' => $logs,
        ]);
    }

    public function editArticle($id, Request $request)
    {
        $article = ArticlePost::find($id);

        return view('admin.content.articles.edit')->with([
            'article' => $article,
        ]);
    }

    public function updateArticle($id, Request $request)
    {
        $validate = $request->validate([
            'title' => 'required|max:255',
            'url' => 'required|url',
            'image_url' => 'url',
        ]);

        $article = ArticlePost::find($id);
        
        $article->update([
            'title' => $request->input('title'),
            'url' => $request->input('url'),
            'localization' => $request->localization,
        ]);

        if($request->has('custom_image_upload')) {  
            $name = $request->custom_image_upload->store('images', 's3');
            // optimizeImage(public_path() . $name, 1800);
            $article->update(['image_url' => $name]);
        }

        $post = Post::where('post_type', '=', 'App\ArticlePost')
            ->where('post_id', '=', $id)
            ->first();
        $post->update([
            'post_at' => Carbon::parse($request->date . ' ' . $request->time . ' ' . $request->user()->timezone)->tz('UTC')->toDateTimeString(),
        ]);
        $post->groups()->sync($request->input('groups'));

        return redirect('/admin/content/articles/' . $id);
    }

    public function deleteArticle($id, Request $request)
    {
        $article = ArticlePost::find($id);
        $article->listing->groups()->sync([]);
        $article->listing->delete();
        $article->delete();

        return redirect('/admin/content/');
    }

    public function editFeed($id, Request $request)
    {
        $feed = Feed::find($id);

        return view('admin.content.feeds.edit')->with([
            'feed' => $feed,
        ]);
    }

    public function updateFeed($id, Request $request)
    {
        $feed = Feed::find($id);
        $feed->update([
            'name' => $request->input('name'),
            'url' => $request->input('url'),
            'type' => $request->input('type'),
            'status' => 'not yet run',
        ]);

        $feed->groups()->sync($request->input('groups'));

        return redirect('/admin/content/feeds');
    }

    public function deleteFeed($id, Request $request)
    {
        $feed = Feed::find($id);
        $feed->groups()->sync([]);
        $feed->delete();

        return redirect('/admin/content/feeds');
    }

    public function export(Request $request, Excel $excel)
    {
        $start = $request->start_date ? Carbon::parse($request->start_date) : null;
        $end = $request->end_date ? Carbon::parse($request->end_date) : null;
        return $excel->download(new ContentExport($start, $end), 'articles.xlsx');
    }
}
