<?php

namespace App\Http\Controllers\Campaign;

use App\Campaign;
use App\CampaignParticipant;
use App\Participant;
use App\Donation;

use App\Http\Controllers\Controller;
use App\Traits\CalendarTrait;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use CalendarTrait;

    public function login()
    {
        return view('welcome.auth-base-campaign-basic');
    }

    public function createMissingDefaultCampaign($team)
    {
        // Get First Tuesday of Upcoming November as Default Data
        $november = collect(['1 November'])->map(function ($item) {
            $date = Carbon::parse($item);

            return $date->isPast() ? $date->addYear() : $date;
        });
        $first_tuesday = Carbon::parse($november[0]->toDateString())->firstOfMonth(2);

        $campaign = new Campaign;
        $campaign->current = true;
        $campaign->name = 'Default Campaign';
        $campaign->team_id = $team->id;
        $campaign->user_id = Auth::user()->id;
        $campaign->election_day = $first_tuesday->toDateString();
        $campaign->save();

        return $campaign;
    }

    public function dashboard()
    {
        $campaigns = Campaign::where('team_id', Auth::user()->team->id)->first();

        if (! $campaigns) {
            $campaign_current = $this->createMissingDefaultCampaign($team = Auth::user()->team);
        } else {
            $campaign_current = Campaign::where('team_id', Auth::user()->team->id)
                                        ->where('current', true)
                                        ->first();
        }

        $support_data = CampaignParticipant::where('team_id', Auth::user()->team->id)
                                           ->where('campaign_id', $campaign_current->id)
                                           ->whereNotNull('support')
                                           ->select('support', DB::raw('count(*) as total'))
                                           ->groupBy('support')
                                           ->get()
                                           ->toArray();

        $support = [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

        foreach ($support_data as $the_level) {
            $support[$the_level['support']] = $the_level['total'];
        }

        $lists = Auth::user()->team->campaignLists()->latest()->take(5)->get();

        $support_max = max($support);
        $support_sum = array_sum($support);

        $donations_recent = Donation::where('team_id', Auth::user()->team->id)->take(10)->get();

        $support_recent = CampaignParticipant::where('campaign_id', $campaign_current->id)
                                             ->whereNotNull('support')
                                             ->orderBy('updated_at', 'desc')
                                             ->take(10)
                                             ->get();
        $events_json = $this->getCampaignCalendarEventsJson(3);

        $notice = \App\Models\Admin\Notice::where('app_type', Auth::user()->team->app_type)
                                          ->where('approved', true)
                                         ->whereNull('archived_at')
                                          ->where('publish_at', '<=', Carbon::now()->toDateTimeString())
                                          ->orderBy('publish_at', 'desc')
                                          ->first();

        return view('campaign.dashboard.main', compact('notice',
                                                       'campaigns',
                                                       'campaign_current',
                                                       'support',
                                                       'support_sum',
                                                       'support_max',
                                                       'donations_recent',
                                                       'support_recent',
                                                       'events_json',
                                                       'lists'));
    }
}
