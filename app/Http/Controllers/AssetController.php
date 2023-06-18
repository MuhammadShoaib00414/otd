<?php

namespace App\Http\Controllers;

use App\Helpers\EmailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only('emailTemplate');
    }

    public function logo()
    {
        $logo_url = \App\Setting::where('name', 'logo')->first()->getRawOriginal('value');

        return response()->redirectTo(getS3Url($logo_url));
    }

    public function emailTemplate(EmailHelper $helper)
    {
        return $helper->replaceColors(view('assets.emailtemplate'));
    }
}
