<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class AppGate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $app_type)
    {
        if (Auth::user()->team->app_type != $app_type) {
            return abort('403');
        }

        return $next($request);
    }
}
