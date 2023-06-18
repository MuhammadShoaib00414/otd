<?php

use App\Badge;
use App\Export;
use App\Option;
use App\Setting;
use App\ArticlePost;
use App\HomePageImage;
use App\IdeationArticle;
use App\RegistrationPage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixFilePaths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // go through each possible model's attributes and remove anything related to /uploads or app url
        // this is to ensure a smooth transition from one filesystem to another

        $toRemove = ['/uploads/', 'uploads/', config('app.url') . '/uploads/', config('app.url')];

        foreach(Setting::all() as $setting)
        {
            $new_path = str_replace($toRemove, '', $setting->getRawOriginal('value'));
            if(substr($new_path, 0, 1) == '/')
                $new_path = substr($new_path, 1);
            $setting->update([
                'value' => $new_path,
            ]);
        }

        ArticlePost::chunk(20, function ($articles) use ($toRemove) {
            foreach($articles as $article)
            {
                $old_url = $article->getRawOriginal('image_url');
                $new_url = str_replace($toRemove, '', $old_url);
                if(filter_var($new_url, FILTER_VALIDATE_URL))
                    $new_url = parse_url($new_url)['path'];
                if(substr($new_url, 0, 1) == '/')
                    $new_url = substr($new_url, 1);
                $article->update(['image_url' => $new_url]);
            }
        });

        Badge::chunk(20, function($badges) {
            foreach($badges as $badge)
            {
                $icon = Storage::disk('s3')->url($badge->icon);
                if(substr($icon, 0, 1) == '/')
                {
                    $icon = substr($icon, 1);
                    $badge->update(['icon' => $icon]);
                }
            }
        });

        Export::whereNotNull('path')->chunk(20, function($exports) {
            foreach($exports as $export)
            {
                $array = explode('/uploads/', $export->path);
                $new_path = end($array);
                $export->update([
                    'path' => $new_path,
                ]);
            }
        });

        HomePageImage::chunk(20, function($home_images) {
            foreach($home_images as $home_page_image)
            {
                $new_path = str_replace('/uploads/', '', $home_page_image->getRawOriginal('image_url'));

                $home_page_image->update([
                    'image_url' => $new_path,
                ]);
            }
        });

        IdeationArticle::chunk(20, function($articles) use ($toRemove) {
            foreach($articles as $article)
            {
                $old_url = $article->getRawOriginal('image_url');
                $new_url = str_replace($toRemove, '', $old_url);
                if(filter_var($new_url, FILTER_VALIDATE_URL) && str_contains($new_url, config('app.url')))
                    $new_url = parse_url($new_url)['path'];
                if(substr($new_url, 0, 1) == '/')
                    $new_url = substr($new_url, 1);
                $article->update(['image_url' => $new_url]);
            }
        });

        Option::whereNotNull('icon_url')->chunk(20, function($options) {
            foreach($options as $option)
            {
                $url = $option->getRawOriginal('icon_url');
                if(str_contains($url, 'uploads/'))
                {
                    $url = str_replace('uploads/', '', $url);
                    $option->update([
                        'icon_url' => $url,
                    ]);
                }
            }
        });

        RegistrationPage::whereNotNull('image_url')->chunk(20, function($pages) {
            foreach($pages as $page)
            {
                $page->update([
                    'image_url' => str_replace('/uploads/', '', $page->getRawOriginal('image_url')),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
