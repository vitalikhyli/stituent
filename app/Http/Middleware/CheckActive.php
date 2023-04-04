<?php

namespace App\Http\Middleware;

use Auth;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Str;

class CheckActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // Check Sandbox time limit

        if (Str::startsWith(Auth::user()->team->name, 'SANDBOX:')) {
            if (Carbon::parse(Auth::user()->last_login)->diffInSeconds() >= 60 * config('app.sandbox_timeout')) {
                $app_type = Auth::user()->team->app_type;
                Auth::logout();
                if ($app_type == 'u') {
                    return route('welcome-u');
                }

                return route('login');
            }
        }

        // Check Active

        if (! Auth::user()->team->account->active) {
            $inactive_levels['account'] = true;
        }
        if (! Auth::user()->team->active) {
            $inactive_levels['team'] = true;
        }
        if (! Auth::user()->active) {
            $inactive_levels['user'] = true;
        }

        if (isset($inactive_levels)) {
            if (! request()->ajax()) {
                //dd($inactive_levels);
                return redirect('/inactive')->with('inactive_levels', $inactive_levels);
            }
        }

        return $next($request);
    }
}
