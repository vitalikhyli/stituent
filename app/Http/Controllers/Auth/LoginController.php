<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
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

    // protected $redirectTo = '/home';

    public function keepUserLoggedIn()
    {
        $keep_me_logged_in = (request('keep-me-logged-in')) ? true : false;
        Auth::loginUsingId(Auth::user()->id, $keep_me_logged_in);
    }

    public function authenticated()
    {
        $this->keepUserLoggedIn();

        Auth::user()->last_login = now();
        Auth::user()->save();

        $dont_ask_for_terms_yet = true;

        if ((Auth::user()->accepted_terms) || ($dont_ask_for_terms_yet)) {
            $url = Auth::user()->team->app_type;
        } else {
            $url = '/tos';
        }

        return redirect($url);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');

        $this->username = $this->whichLoginHandle(); //ADDED THIS
    }

    // ADDED THESE TO HANDLE USERNAME LOGIN TOO
    //
    // https://tutsforweb.com/laravel-auth-login-email-username-one-field/
    //
    //

    public function whichLoginHandle()
    {
        $login = request()->input('email');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    public function logout()
    {
        $app_type = Auth::user()->team->app_type;
        Auth::logout();
        session()->flush();

        return redirect('/'.$app_type);
    }
}
