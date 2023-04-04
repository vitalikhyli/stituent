<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Person;
use App\Participant;

use Auth;


class VoterLinkController extends Controller
{
    public function index($app_type)
    {
        if ($app_type == 'office') {
        	$individuals = Person::where('team_id', Auth::user()->team->id)
        						  ->whereNull('voter_id')
        						  ->paginate(10);
        }

    
        if ($app_type == 'campaign') {
            $individuals = Participant::where('team_id', Auth::user()->team->id)
                                      ->whereNull('voter_id')
                                      ->paginate(10);
        }   

    	return view('shared-features.voter-link.index', ['individuals' => $individuals]);
    }


}
