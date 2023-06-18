<?php 

namespace App\OTD;

use App\Post;
use App\Feed;
use Carbon\Carbon;
use App\ArticlePost;
use GuzzleHttp\Client;

class FeedProcesser
{

    protected $stopAfterThisManyAlreadyExistsInDB = 12;

    public function __invoke()
    {
        $this->fetchFeeds();
    }

    public function fetchFeeds()
    {
        $feeds = Feed::all();

        foreach ($feeds as $feed) {
            if ($feed->type == 'json')
                $this->processJsonFeed($feed);
        }
    }

    public function processJsonFeed($feed)
    {
        $feed->update([
            'status' => 'processing',
        ]);
        $response = $this->fetchAndProcessJsonUrl($feed->url, $feed);
        $feed->update([
            'status' => 'processed',
            'last_processed_at' => Carbon::now()->toDateTimeString(),
        ]);
    }

    public function fetchAndProcessJsonUrl($url, $feed)
    {
        $client = new Client();
        $response = $client->get($url);
        $articles = json_decode($response->getBody());
        $numberThatExists = 0;
        foreach ($articles->data as $article) {
            if ($this->processArticle($article, $feed)) {
                $numberThatExists = $numberThatExists + 1;
                if ($this->stopAfterThisManyAlreadyExistsInDB == $numberThatExists)
                    break;
            } else {
                $numberThatExists = 0;
            }
        }
        if (isset($articles->meta->pagination->links->next) && $this->stopAfterThisManyAlreadyExistsInDB != $numberThatExists)
            $this->fetchAndProcessJsonUrl($articles->meta->pagination->links->next, $feed);
    }

    public function processArticle($article, $feed)
    {
        if ($existing = $this->fetchFromDB($article, $feed)) {
            $existing->listing->groups()->sync($feed->groups);

            return true;
        } else {
            $this->createContentPost($article, $feed);

            return false;
        }
    }

    public function fetchFromDB($article, $feed)
    {
        return ArticlePost::where('url', '=', $article->url)->first();
    }

    public function createContentPost($article, $feed)
    {
        $articlePost = ArticlePost::create([
            'url' => $article->url,
            'title' => $article->title,
            'image_url' => $article->image,
        ]);
        $post = Post::create([
            'post_type' => get_class($articlePost),
            'post_id' => $articlePost->id,
            'post_at' => Carbon::parse($article->date)->toDateTimeString(),
        ]);
        $post->groups()->attach($feed->groups);
    }
}