<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MobileLink extends Model
{
    protected $guarded = ['id'];

    protected $fillable = ['icon_url', 'url', 'defaults', 'is_editable'];

    protected $casts = ['defaults' => 'array'];

    public function restoreDefaults()
    {
        $this->update([
            'icon_url' => $this->defaults['icon_url'],
            'url' => $this->defaults['url'],
        ]);
    }
}
