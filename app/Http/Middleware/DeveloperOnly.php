<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class DeveloperOnly
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
        if (! Auth::user()->permissions->developer) {
            return abort('403');
        }

        return $next($request);
    }
}
