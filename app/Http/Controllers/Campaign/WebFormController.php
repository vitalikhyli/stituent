<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\WebForm;
use App\WebSignup;
use App\Campaign;
use Illuminate\Support\Str;
use Auth;
use App\Notifications\Error;
use App\User;

class WebFormController extends Controller
{
    public function index()
    {
    	$webforms = WebForm::withTrashed()->with('webSignups')->thisTeam()->get();
        $websignups = WebSignup::thisTeam()->get();
    	return view('campaign.web-forms.index', compact('webforms', 'websignups'));
    }

    public function store()
    {
        $webform = new WebForm;
        $webform->account_id = Auth::user()->team->account_id;
        $webform->team_id = Auth::user()->team->id;
        $webform->user_id = Auth::user()->id;
        $webform->name = request('name');
        $webform->unique_id = Str::random(20);
        $webform->save();
        return redirect()->back();
    }

    public function signUp(Request $request, $id)
    {
        $webform = WebForm::where('unique_id', $id)->first();
        if ($webform) {
            $websignup = new WebSignup;
            $websignup->account_id =  $webform->account_id;
            $websignup->team_id =     $webform->team_id;
            $websignup->user_id =     $webform->user_id;
            $websignup->web_form_id = $webform->id;
            $websignup->name =        request('name');
            $websignup->email =       request('email');
            $websignup->note =        request('note');

            $websignup->data =        ['volunteer' => request('volunteer'),
                                       'location'  => request('location')];

            //dd($request->headers->toArray());
            $websignup->meta =        ['ip' => $request->ip()];
            $websignup->save();

            $message = "Thank you!";

            return view('campaign.web-forms.iframe', compact('webform', 'message'));
        }
        return redirect()->back();
    }

    public function iframe($id)
    {
        $message = '';
    	$webform = WebForm::where('unique_id', $id)->first();

        if (!$webform) {
            $user = User::find(257); // Laz Admin
            $user->notify(new Error("Bad web form id: ".$id));
        }

        $campaign = Campaign::where('team_id', $webform->team_id)
                           ->where('current', true)
                           ->first();
        session(['current_campaign' => $campaign]);

        $volunteer_options = $campaign->volunteer_options($participant = null, $keep_prefix = false);
    	return view('campaign.web-forms.iframe', compact('volunteer_options', 'webform', 'message'));
    }
}
