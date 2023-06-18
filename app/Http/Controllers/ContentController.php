<?php

namespace App\Http\Controllers;

use App\ArticlePost;
use Illuminate\Http\Request;

class ContentController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }

    public function logClick($articleId, Request $request)
    {
        ArticlePost::find($articleId)->increment('clicks');

        return redirect($request->next);
    }
}
