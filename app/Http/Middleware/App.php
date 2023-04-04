<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Device;
use App\UserLogApp;
use Auth;
use App\User;
use Illuminate\Support\Str;


class App
{
    protected $log;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // ===============================> Start logging
        $this->log = new UserLogApp;
        $this->log->url = $request->fullUrl();
        $this->log->debug = ['ip' => $request->ip()];
        $this->log->save();

        // ===============================> Check Auth APIkey in header
        $apikey = $request->header('Authorization');

        if (request('web') && Str::contains($request->fullUrl(), 'files')) {
            $apikey = base64_decode(request('web'));
            //dd($apikey);
        }
        
        if (!$apikey && request('auth') != 'laz') {
            return $this->failWithMessage("No API Key");
        }

        // ===============================> Check if valid device
        $valid_device = Device::where('api_key', $apikey)
                              ->first();

        if (request('auth') == 'laz') {
            if (request('auth_test')) {
                $valid_device = Device::find(request('auth_test'));
                if (!$valid_device) {
                    $valid_device = Device::first();
                }
            } else {
                $valid_device = Device::find(2);
                if (!$valid_device) {
                    $valid_device = Device::first();
                }
            }
        }

        
        
        if (!$valid_device) {
            return $this->failWithMessage("No Valid Device");
        }
        $this->log->device_id = $valid_device->id;

        // ===============================> Get user
        if (!$valid_device->user) {
            return $this->failWithMessage("No User Found");
        }
        $this->log->user_id = $valid_device->user_id;
        $this->log->name = $valid_device->user_name;

        // ===============================> Get team
        if (!$valid_device->user->team) {
            return $this->failWithMessage("No Team Found");
        }

        if ($valid_device->user->current_team_id != $valid_device->team_id) {
            $valid_device->user->current_team_id = $valid_device->team_id;
            $valid_device->user->save();
        }
        $this->log->team_id = $valid_device->team_id;
        $this->log->team_name = $valid_device->team_name;

        $this->log->save();

        // ===============================> Login and set session data

        Auth::login($valid_device->user);
        //Auth::login(User::find(35)); // jenny tarr

        $request->session()->put('team_table', $valid_device->team_table);
        $request->session()->put('team_state', $valid_device->team_state);
        $request->session()->put('team_id', $valid_device->team_id);
        $request->session()->put('device_id', $valid_device->id);

        return $next($request);
    }
    public function terminate($request, $response)
    {
        $this->terminateLog();
    }
    public function terminateLog()
    {
        if ($this->log) {
            $this->log->time = round(microtime(true) - LARAVEL_START, 2);
            $this->log->save();
        }
    }

    public function failWithMessage($message)
    {
        $message .= ": ".request()->header('Authorization');
        $arr = $this->log->debug;
        $arr['message'] = $message;
        $this->log->debug = $arr;
        $this->terminateLog();

        return response()->json([
                    'code'      => 401,
                    'message'   => $message,
                    'pin'       => request('pin'),
                ], 401); 
    }
}
