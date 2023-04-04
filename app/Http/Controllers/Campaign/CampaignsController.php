<?php

namespace App\Http\Controllers\Campaign;

use App\Campaign;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CampaignsController extends Controller
{
    public function index()
    {
        $campaigns = Campaign::where('team_id', Auth::user()->team->id)
                             ->orderBy('election_day', 'desc')
                             ->get();

        return view('campaign.campaigns.index', compact('campaigns'));
    }

    public function edit($id)
    {
        $campaign = Campaign::find($id);

        return view('campaign.campaigns.edit', compact('campaign'));
    }

    public function delete($id)
    {
        $campaign = Campaign::find($id);
        $campaign->delete();

        return redirect(Auth::user()->team->app_type.'/campaigns');
    }

    public function update(Request $request, $id, $close = null)
    {
        $campaign = Campaign::find($id);

        $this->authorize('basic', $campaign);

        if (request('votes_needed') === null || request('votes_needed') === 0) {
            $votes_needed = null;
        } else {
            $votes_needed = str_replace(',', '', request('votes_needed'));
        }

        $campaign->name = request('name');
        $campaign->election_day = Carbon::parse(request('election_day'))->toDateString();
        $campaign->current = (request('current')) ? true : false;
        $campaign->votes_needed = $votes_needed;
        $campaign->save();

        // Make other campaigns not current

        if ($campaign->current) {
            $other_current = Campaign::where('team_id', Auth::user()->team->id)
                           ->where('current', true)
                           ->where('id', '<>', $campaign->id)
                           ->get();
            foreach ($other_current as $other) {
                $other->current = false;
                $other->save();
            }
        }

        // At least one campaign has to be the current campaign

        $current = Campaign::where('team_id', Auth::user()->team->id)
                           ->where('current', true)
                           ->first();

        if (! $current) {
            $campaign->current = true;
            $campaign->save();
        }

        session()->forget('current_campaign');

        if ($close) {
            return redirect('/'.Auth::user()->team->app_type.'/campaigns/');
        } else {
            return redirect('/'.Auth::user()->team->app_type.'/campaigns/'.$id.'/edit');
        }
    }

    public function store(Request $request)
    {
        $campaign = new Campaign;
        $campaign->name = request('name');
        $campaign->team_id = Auth::user()->team->id;
        $campaign->user_id = Auth::user()->id;
        $campaign->save();

        return redirect('/'.Auth::user()->team->app_type.'/campaigns/'.$campaign->id.'/edit');
    }
}
