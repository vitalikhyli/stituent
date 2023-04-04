<?php

namespace App\Http\Middleware;

use App\UserLog;
use Auth;
use Closure;

class UserLogMiddleware
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
        return $next($request);
    }

    public function terminate($request, $response)
    {
        // use session data to log what is needed
        // title of page or action taken
        // session data is possible to reach here

        if ($request->ajax()) {

            return;
            $type = 'wire';

        } elseif (isset($request->header()['x-livewire'][0])) {

            $type = 'wire';

        } else {

            $type = null;

        }

        if (Auth::check()) {
            $userlog = new UserLog;
            if (session('mocking')) {
                $userlog->mock_id = session('mocking');
            }
            $userlog->user_id   = Auth::user()->id;
            $userlog->team_id   = Auth::user()->current_team_id;
            $userlog->username  = Auth::user()->username;
            $userlog->name      = Auth::user()->name;
            $userlog->type      = $type;
            $userlog->url       = $request->fullUrl();
            $userlog->time      = round(microtime(true) - LARAVEL_START, 2);
            $userlog->save();
        }
    }
}
