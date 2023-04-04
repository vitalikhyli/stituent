<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class FullUsersOnly
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
        if (Auth::user()->permissions->guest) {
            return abort('403');
        }

        return $next($request);
    }
}
