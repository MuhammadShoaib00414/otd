<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\EnabledScope;

class Badge extends Model
{
    protected $guarded = [
        'id',
    ];

    public $timestamps = false;

    protected $casts = [
    	'localization' => 'array'
    ];

    // public function newQuery($whereEnabled = true)
    // {
    //     $query = parent::newQuery(true); //true for exclude deleted

    //     if($whereEnabled)
    //         $query = $query->where('is_enabled', 1);

    //     return $query;
    // }

    protected static function boot()
    {
        parent::boot();    
        static::addGlobalScope(new EnabledScope);
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function getNameAttribute($value)
    {
    	return localizedValue('name', $this->localization) ?: $value;
    }

    public function getIconAttribute($value)
    {
        return '/' . $value;
    }
}
