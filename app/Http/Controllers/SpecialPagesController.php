<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CampaignParticipant;
use App\CampaignList;
use App\Voter;
use Auth;

class SpecialPagesController extends Controller
{
    public function office()
    {
    	return view('office.special-pages.index');
    }
    public function campaign()
    {
    	return view('campaign.special-pages.index');
    }
    public function householdMembers()
    {
    	$list = null;
    	if (request('list')) {
    		$list = CampaignList::find(request('list'));
    		if ($list->team_id != Auth::user()->team_id) {
    			$list = null;
    		}
    	}
    	$voters = CampaignParticipant::thisTeam()
    								 ->hasSupport()
    								 ->pluck('voter_id');

    	if ($list) {
    		//
    		$list_ids = array_keys($list->cached_voters);

			$voters = $voters->intersect($list_ids);
			//dd($voters, $newvoters, $list_ids);
    	}
    	$houses = Voter::whereIn('id', $voters)
    				   ->orderBy('household_id')
    				   ->pluck('household_id')
    				   ->unique();

    	$all_voters = Voter::whereIn('household_id', $houses)
    					   ->whereNull('archived_at')
    					   ->orderBy('household_id')
    					   ->orderBy('last_name')
    					   ->orderBy('first_name')
    					   ->get()
    					   ->groupBy('household_id');
    	$grouped_by_household = collect([]);
    	foreach ($all_voters as $household_id => $voters) {
    		if ($voters->count() > 1) {
	    		$grouped_by_household[$household_id] = $voters;
	    	}
    	}

    	return view('campaign.special-pages.household-members', compact('grouped_by_household'));
    }
}
