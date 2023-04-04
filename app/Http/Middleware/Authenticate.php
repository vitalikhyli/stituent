<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if ($request->path() == 'u') {
                return route('welcome-u');
            }
            // if ($request->path() == 'campaign') return route('welcome-campaign');
            if ($request->path() == 'campaign') {
                return 'http://campaignfluency.com/';
            }
            // if ($request->path() == 'office')   return route('login');
            return route('login');
        }
    }
}
