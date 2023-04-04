<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CampaignParticipant extends Model
{
    protected $table = 'campaign_participant';

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }
    public function scopeHasSupport($query)
    {
        return $query->where('support', '>', 0);
    }

    public function scopeThisCampaign($query)
    {
        $current_campaign = Campaign::thisTeam()->where('current', true)->first();
        if ($current_campaign) {
            return $query->where('campaign_id', $current_campaign->id);
        }
        return $query;
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function isVolunteer()
    {
        $attrs = $this->getAttributes();
        //dd($attrs);
        foreach ($attrs as $attr => $val) {
            if (Str::contains($attr, 'volunteer')) {
                if ($val > 0) {
                    return true;
                }
            }
        }
        return false;
    }
    public function getSupportName()
    {
        if (!$this->support) {
            return null;
        }
        $support_names = [
            1 => 'Yes',
            2 => 'Lean Yes',
            3 => 'Neutral',
            4 => 'Lean No',
            5 => 'No',
        ];
        return $support_names[$this->support];
    }
    public function getVolunteering()
    {
        $attrs = $this->getAttributes();
        $volunteering = collect([]);
        foreach ($attrs as $attr => $val) {
            if (Str::contains($attr, 'volunteer')) {
                if ($val > 0) {
                    $volunteering[] = ucwords(str_replace(['volunteer_', '_'], ['', ' '], $attr));

                }
            }
        }
        return $volunteering->implode(', ');
    }

    public function save(array $options = [])
    {
        if ($this->isDirty('support')) {
            if ($this->support > 0) {
                addActionFromObject($this, 'Support '.$this->support, $this->getSupportName(), null);
            }
        }
        if ($this->isDirty('notes')) {
            addActionFromObject($this, 'Added Note', $this->notes, null);
        }
        $volunteer_updates = [];
        foreach ($this->getAttributes() as $attr => $val) {
            //dd("Laz", $attr, $this->getAttributes());
            if (substr($attr, 0, 10) == 'volunteer_') {
                //dd($attr);
                if ($this->$attr == 1) {
                    //dd("Laz"); 
                    $volname = ucwords(str_replace(['volunteer_', '_'], ['',' '], $attr));
                    $volunteer_updates[] = $volname;

                }
            }
        }
        if (count($volunteer_updates) > 0) {
            //dd($volunteer_updates);
            $action_name = "Volunteer Update";
            $action_details = implode(', ', $volunteer_updates);
            //dd($action_details);
            $action = addCustomActionToParticipant($this->participant, $action_name, $action_details, null, true);
            //dd($action);
        }
        return parent::save($options);
    }
}
