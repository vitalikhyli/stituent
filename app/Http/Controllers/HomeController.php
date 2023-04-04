<?php

namespace App\Http\Controllers;

use App\Mail\DemoRequest;
use App\Traits\CFCaptchaTrait;
use Auth;
use Illuminate\Http\Request;
use Log;
use Mail;

class HomeController extends Controller
{
    use CFCaptchaTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function inactive($inactive_levels = null)
    {
        //dd($inactive_levels);
        return view('shared-features.messages.inactive', compact('inactive_levels'));
    }

    public function whichDashboard()
    {
        return redirect(Auth::user()->team->app_type);
    }

    public function captcha()
    {
        return view('index', ['data'=>$imstr]);
    }

    public function requestDemo()
    {
        // $cf_captcha = $this->CFCaptcha();
        // $captcha_image = $cf_captcha['image'];
        // $captcha_code = $cf_captcha['code'];

        // return view('welcome.request-demo', compact('captcha_image', 'captcha_code'));

        return view('welcome.request-demo');
    }

    public function requestDemoSend(Request $request)
    {
        // if (! $this->CFCaptchaCheck(request('captcha_code_actual'),
        //                         request('captcha_code_submit'))) {
        //     return back();
        // }

        if (request('first_name') || request('last_name')) {
            //Honey Pot
            return back();
        }

        Log::stack(['demo_requests'])->info(\Carbon\Carbon::now().' '.request('email').' '.request('email').' '.request('notes'));

        $recipient = 'laz@communityfluency.com';

        try {
            Mail::to($recipient)->send(new DemoRequest($request));
        } catch (\Exception $e) {
            Log::stack(['demo_requests'])->info('Email error. '.$e);

            $demo_error = true;

            return view('auth.login', compact('demo_error'));
        }

        $demo = true;

        return view('auth.login', compact('demo'));
    }

    public function acceptTerms()
    {
        Auth::user()->acceptTerms();

        return redirect(Auth::user()->team->app_type);
    }

    public function index()
    {
        return view('constituents.dashboard');
    }
}
