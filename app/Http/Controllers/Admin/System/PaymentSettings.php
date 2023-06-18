<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;

class PaymentSettings extends Controller
{
    public function edit()
    {
        return view('admin.system.payment-configuration')->with([

        ]);
    }

    public function store(Request $request)
    {
        Setting::where('name', 'stripe_key')->update([
            'value' => Crypt::encrypt($request->stripe_key),
        ]);
        Setting::where('name', 'stripe_secret')->update([
            'value' => Crypt::encrypt($request->stripe_secret),
        ]);

        \Illuminate\Support\Env::getRepository()->set('STRIPE_KEY', $request->stripe_key);
        \Illuminate\Support\Env::getRepository()->set('STRIPE_SECRET', $request->stripe_secret);

        Cache::forget('settings');

        return redirect()->back()->with('success', 'Settings saved!');
    }
}
