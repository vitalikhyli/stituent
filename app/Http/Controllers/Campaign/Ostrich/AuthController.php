<?php

namespace App\Http\Controllers\Campaign\Ostrich;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

use App\Models\Campaign\Volunteer;

use App\Notifications\CampaignGuestLink;

use Auth;
use Carbon\Carbon;


class AuthController extends Controller
{
	public function sendLink(Request $request) {

		// Honeypot
		if (request('name')) {
			return redirect()->back();
		}

		$guest = Volunteer::where('email', request('email'))
					  ->where('active', true)
					  ->first();

		if (!$guest) {
			return redirect()->back();
		}

		$guest->regenerateUUID();

		try {

        	Notification::route('mail', $guest->email)->notify(new CampaignGuestLink($guest));

        } catch (\Exception $e) {

        	//

        }

        $guest->recordNotication();

		return redirect()->back()->with('link_sent', 1);
	}

	public function logout()
	{
		$guest = CampaignGuest();
		
		if ($guest) {
			$guest->endSession();
		}
		
		if(Auth::check()) {
			return redirect('/campaign');
		} else {
			return redirect('/ostrich');
		}
	}

	public function loginByLink($uuid)
	{
		$guest = Guest::where('uuid', $uuid)
					  ->where('uuid_expires_at', '>=', Carbon::now())
					  ->where('active', true)
					  ->first();
		
		if ($guest) {
			$guest->createSession();
			return redirect('/ostrich/dashboard');
		}

		return redirect('/ostrich')->with('errors', ['Link has expired.']);
	}

	public function loginUser()
	{
		$user = Auth::user();

		$guest = Volunteer::where('user_id', $user->id)->first();

		if (!$guest) {
			$guest = new Volunteer();
			$guest->createNewFromUser($user);
			$guest->save();
		}

		if ($guest && $guest->active) {
			$guest->createSession();
			return redirect('/ostrich/dashboard');
		}

		return redirect()->back();
	}

    public function loginPage()
    {
    	if (CampaignGuest()) return redirect('/ostrich/dashboard');
		return view('campaign.ostrich.login-page');
    }

}
