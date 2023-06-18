<?php

use App\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixLocalizedFilePaths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $toRemove = ['/uploads/', 'uploads/', config('app.url') . '/uploads/', config('app.url')];

        foreach(Setting::all() as $setting)
        {
            if(!$setting->is_file)
                continue;

            $localization = $setting->localization;

            if(!is_array($localization) || !array_key_exists('es', $localization))
                continue;
            if(!array_key_exists($setting->name, $localization['es']))
                continue;
            if($localization['es'][$setting->name] == '')
                continue;

            $value = $localization['es'][$setting->name];

            $new_path = str_replace($toRemove, '', $value);
            if(substr($new_path, 0, 1) == '/')
                $new_path = substr($new_path, 1);

            $new_localization['es'][$setting->name] = $new_path;

            $setting->update([
                'localization' => $new_localization,
            ]);

            $new_localization = null;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
