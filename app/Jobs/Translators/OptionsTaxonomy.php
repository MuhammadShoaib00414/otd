<?php

namespace App\Jobs\Translators;

use App\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class OptionsTaxonomy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    
    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lastInsertId = $this->id;
    
        // get the data from the database using the ID
        $options_posts = DB::table('options')->where('id', $lastInsertId)->first();
    
        $is_multi_languages = Setting::where('name', 'is_multi_languages')->first()->value;
        $targetLanguages = json_decode($is_multi_languages);
        // Retrieve the text to be translated
        $translationBody = [];
        foreach ($targetLanguages as $key => $targetLanguage) {
            $name = awsTranslaterSettings()->TranslateText([
                'Text' => strip_tags($options_posts->name),
                'SourceLanguageCode' => 'auto',
                'TargetLanguageCode' => $targetLanguage,
            ])['TranslatedText'];
            $translationBody[$targetLanguage] = [
                "name" => $name
            ];
        }
        $data = [
            'localizationInsert' =>  DB::table('options')->where('id',  $lastInsertId)->update(['localization' => json_encode($translationBody, true)]),
        ];
        return $data;
    }
}
