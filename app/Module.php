<?php

namespace App;

use App\ModuleUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function sequence()
    {
        return $this->belongsTo(Sequence::class);
    }

    public function getThumbnailImagePathAttribute($value)
    {
        if(!$value)
            return '';

        return getS3Url($value);
    }

    public function hasUserCompleted($user = null)
    {
        if ($user == null)
            $user = request()->user();

        $moduleUser = ModuleUser::where([
            'user_id' => $user->id,
            'module_id' => $this->id,
        ])->first();

        if (!$moduleUser)
            return false;

        return $moduleUser->completed_at != null;
    }

    public function getNextModuleAttribute()
    {
        $modules = $this->sequence->modules()->orderBy('order_key', 'asc')->get();

        $remainingIncludingThisOne = $modules->skipWhile(function ($module) {
            return $module->id != $this->id;
        });


        if ($remainingIncludingThisOne->count() < 2)
            return null;

        return $remainingIncludingThisOne->values()[1];
    }
}
