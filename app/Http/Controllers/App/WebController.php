<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Device;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Notifications\SendAppPin;
use App\User;


class WebController extends Controller
{
    public function main()
    {
    	return view('app.main');
    }

    public function emailCode()
    {
        $email = request('email');
        if (!$email) {
            return response()->json([
                    'code'      => 200,
                    'message'   => "Please use demo PIN code 02108 below!",
                ], 200);
        }
        $user = User::where('email', 'LIKE', $email)->latest()->first();
        if (!$user) {
            return response()->json([
                    'code'      => 401,
                    'message'   => "No user found with email ".$email,
                ], 401);
        }
        if (!$user->active) {
            return response()->json([
                    'code'      => 401,
                    'message'   => "User is not currently active. Please call 617.699.4553 if this is incorrect.",
                ], 401);
        }
        $pin = $user->current_app_pin;
        $user->notify(new SendAppPin($pin));
        return response()->json([
                    'code'      => 200,
                    'message'   => "5-digit pin has been emailed to ".$email,
                ], 200);
    }

    public function register(Request $request)
    {
    	// ===========================> CHECK SECRET AUTH HEADER
    	$device_auth = $request->header('Authorization');
    	if ($device_auth != 'SUPER_SECRET_CODE_LAZ') {
    		if (!request('test')) {
	    		return $this->failWithMessage('Incorrect Headers');
	    	}
    	}

    	// ===========================> CHECK PIN SENT
    	$pin = request('pin');
    	if (!$pin) {
    		return $this->failWithMessage('Missing PIN');
    	}

        // ===========================> CHECK PIN SAMPLE ACCOUNT


        if ($pin == '44b5c7d484ff6d9c928beda3fe71eb2e') {

            $device = Device::find(9);
            return response()->json([
                    'code'      => 200,
                    'message'   => "Success, ".$device->user->name,
                    'pin'       => md5($device->pin.'zal'),
                    'api_key'   => $device->api_key,
                ], 200); 

        }

    	// ===========================> CHECK PIN VALID

    	if (request('test')) {
    		$pin = md5($pin."laz");
    	}

    	
    	$valid_pin = false;
    	$devices = Device::whereNull('live_at')
    					 ->get();
    	foreach ($devices as $tempdevice) {
    		//echo $pin."\n".md5($tempdevice->pin."laz"));
    		if ($pin == md5($tempdevice->pin."laz")) {
    			$valid_pin = true;
    			$device_id = $tempdevice->id;
    			break;
    		}
    	}
  
    	if (!$valid_pin) {
    		//return $this->failWithMessage("$pin != ".md5($tempdevice->pin."laz"));
    		return $this->failWithMessage("Invalid PIN");
    	}

    	// ===========================> THEY MADE IT!!!

    	$device = Device::find($device_id);
    	$device->device_id 		= request('device_id');
        $device->device_info 	= request('phone_data');
        $device->api_key 		= (string) Str::uuid();
        $device->live_at        = Carbon::now();
    	$device->save();
    	
    	return response()->json([
                    'code'      => 200,
                    'message'   => "Success, ".$device->user->name,
                    'pin'       => md5($device->pin.'zal'),
                    'api_key' 	=> $device->api_key,
                ], 200); 
    }

    public function disconnect()
    {
        $device_id = session()->get('device_id');
        $device = Device::find($device_id);

        if ($device->id != 9) {
            $device->delete();
        }

        return response()->json([
                    'code'      => 200,
                    'message'   => "Success, disconnected",
                ], 200);
    }

    public function failWithMessage($message)
    {
    	return response()->json([
                    'code'      => 401,
                    'message'   => $message,
                    'pin'       => request('pin'),
                ], 401); 
    }
}
