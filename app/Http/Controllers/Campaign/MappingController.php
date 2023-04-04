<?php

namespace App\Http\Controllers\Campaign;

use App\CampaignList;
use App\Http\Controllers\Controller;
use App\Participant;
use App\CampaignParticipant;
use Auth;
use Illuminate\Http\Request;

class MappingController extends Controller
{
    public function index()
    {
        //dd(CampaignList::latest()->first());
        $list = CampaignList::where('team_id', Auth::user()->team_id)->where('name', 'Lawn Signs')->first();
        //dd("Laz");
        if (! $list) {
            //dd("Laz");
            $list = new CampaignList;
            $list->name = 'Lawn Signs';
            $list->team_id = Auth::user()->team_id;
            $list->user_id = Auth::user()->id;
            $list->form = ['volunteers' => true,
                            'volunteers_specific' => ['volunteer_lawnsign'], ];
            $list->save();
        }
        
        //dd($list);
        
        //dd($voters);
        
        //dd($voters_index);

        $participantids = CampaignParticipant::where('team_id', Auth::user()->team_id)
                                  ->where('campaign_id', CurrentCampaign()->id)
                                  ->pluck('participant_id');

        $ids = CampaignParticipant::where('team_id', Auth::user()->team_id)
                                  ->where('campaign_id', CurrentCampaign()->id)
                                  ->whereIn('voter_id', $list->voters()->pluck('id'))
                                  ->pluck('participant_id');

        $voterids = CampaignParticipant::where('team_id', Auth::user()->team_id)
                                  ->where('campaign_id', CurrentCampaign()->id)
                                  ->whereIn('voter_id', $list->voters()->pluck('id'))
                                  ->pluck('voter_id');
                                  //dd($ids);
        $voters = $list->voters()->whereIn('id', $voterids)->get();
        $voters_index = $voters->keyBy('id');
        //dd($voters_index);
        //dd($ids);
        $all_participants = Participant::volunteers(['volunteer_lawnsign'])
                                       ->whereIn('id', $participantids)
                                       ->get();
        //dd($all_participants);
        $extra_participants = collect([]);
        foreach ($all_participants as $participant) {
            if (! isset($voters_index[$participant->voter_id])) {
                $extra_participants[] = $participant;
            }
        }
        $voters = $voters->sortBy('last_name');
        if (request('debug')) {
            dd("Voters", $voters, 
               "Voters Index", $voters_index, 
               "All Participants", $all_participants, 
               "Extra Participants", $extra_participants);
        }
        
        return view('campaign.mapping.index', compact('list', 'voters', 'extra_participants'));
    }
}
