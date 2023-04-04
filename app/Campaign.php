<?php

namespace App;

use App\CampaignParticipant;
// use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Schema;

class Campaign extends Model
{
    // use SoftDeletes;

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function volunteer_options($participant = null, $keep_prefix = null)
    {
        if (! $participant) {
            $columns = Schema::getColumnListing('campaign_participant');
            $volunteer_options = [];

            foreach ($columns as $key => $field) {
                if (substr($field, 0, 10) == 'volunteer_') {
                    if (! $keep_prefix) {
                        $field = str_replace('volunteer_', ' ', $field);
                    }
                    $volunteer_options[] = trim($field);
                }
            }

            return $volunteer_options;
        } else {
            $pivot = CampaignParticipant::where('campaign_id', $this->id)
                                        ->where('participant_id', $participant->id)
                                        ->first();

            if (! $pivot) {
                $pivot = new CampaignParticipant;
                $pivot->team_id = Auth::user()->team->id;
                $pivot->campaign_id = $this->id;
                $pivot->participant_id = $participant->id;
            }

            $columns = Schema::getColumnListing('campaign_participant');

            $volunteer_options = [];

            foreach ($columns as $key => $field) {
                if (substr($field, 0, 10) == 'volunteer_') {
                    $simplified = str_replace('volunteer_', ' ', $field);
                    $simplified = trim(str_replace('_', '-', $simplified));
                    $volunteer_options[$simplified] = ($pivot->$field) ? 1 : 0;
                }
            }

            ksort($volunteer_options);

            $obj = json_decode(json_encode($volunteer_options), false);
        }

        return $obj;
    }

    public function volunteers()
    {
        $volunteer_options = CurrentCampaign()->volunteer_options($participant = null, $keep_prefix = true);

        $volunteer_str = collect($volunteer_options)->implode(' + ');
        
        $ids = CampaignParticipant::where('team_id', Auth::user()->team_id)
                                  ->where('campaign_id', CurrentCampaign()->id)
                                  ->whereRaw('('.$volunteer_str.' > 0)')
                                  ->pluck('participant_id');

        $participants = Participant::thisTeam()
                                   ->whereIn('id', $ids)
                                   ->with('campaignParticipant')
                                   ->get();
        return $participants;
    }

    public function volunteer($participant, $keep_prefix = null)
    {
        $pivot = CampaignParticipant::where('campaign_id', $this->id)
                                    ->where('participant_id', $participant->id)
                                    ->first();

        if (! $pivot) {
            $pivot = new CampaignParticipant;
            $pivot->team_id = Auth::user()->team->id;
            $pivot->campaign_id = $this->id;
            $pivot->participant_id = $participant->id;
        }

        $columns = Schema::getColumnListing('campaign_participant');

        $volunteer = [];

        foreach ($columns as $key => $field) {
            if (substr($field, 0, 10) == 'volunteer_') {
                $simplified = str_replace('volunteer_', ' ', $field);
                $simplified = trim(str_replace('_', '-', $simplified));
                $volunteer[$simplified] = ($pivot->$field) ? 1 : 0;
            }
        }

        $volunteer = collect($volunteer)->reject(function ($item) {
            return $item == null;
        })->toArray();

        ksort($volunteer);

        $obj = json_decode(json_encode($volunteer), false);

        return $obj;
    }
}
