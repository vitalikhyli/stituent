<?php

namespace App\Http\Controllers\Campaign;

use App\Http\Controllers\Controller;
use App\Participant;
use Illuminate\Http\Request;

use App\CampaignParticipant;
use App\Models\Campaign\Volunteer;

use Auth;

use Schema;

class VolunteersController extends Controller
{
    public function indexNew()
    {
        return view('campaign.volunteers.index-new');
    }

    public function index()
    {
    	$volunteer_options = CurrentCampaign()->volunteer_options($participant = null, $keep_prefix = true);

    	$volunteer_str = collect($volunteer_options)->implode(' + ');

        //dd(CurrentCampaign());

    	$participants = Auth::user()->currentCampaign()->volunteers();
                                   //dd($ids);
        $participants_count = $participants->count();

        return view('campaign.volunteers.index', compact('participants', 'participants_count', 'volunteer_options'));
    }
}
