<?php

namespace App\Http\Controllers\Auth;

use Jenssegers\Agent\Agent;
use App\Events\UserSignedIn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function showLoginForm(Request $request)
    {
        $registration_pages = \App\RegistrationPage::where('is_welcome_page_accessible', 1)->get();
        $home_page_images = \App\HomePageImage::where('lang', ($request->has('locale') ? $request->locale : \Illuminate\Support\Facades\App::getLocale()))->get();

        if(!session()->has('url.intended'))
        {
            session(['url.intended' => url()->previous()]);
        }
        return view('auth.login',compact('registration_pages','home_page_images'));
    }

    protected function authenticated(Request $request, $user)
    {
        event(new UserSignedIn($user));
        $agent = new Agent();
        $device = $user->devices
                        ->where('device_type', $agent->platform())
                        ->where('device_name', $agent->device() . ', ' . $agent->browser())->first();

        if ($device && $device->inactive_reason == 'logout') {
            $device->update([
                'active' => true,
                'inactive_reason' => null
            ]);
        }
        if($request->has('next'))
            return redirect($request->next);
    }

    public function logout(Request $request)
    {

        if ( Session::has('oldUser') ){
            \Auth::login(
                Session::get('oldUser')
            );
            

            Session::forget('oldUser');

            return redirect()->route('admin.users');
        }


        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new Response('', 204)
            : redirect('/');
    }
}
