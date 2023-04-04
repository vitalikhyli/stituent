<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CampaignEventInvite extends Model
{
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function event()
    {
        return $this->belongsTo(CampaignEvent::class, 'campaign_event_id', 'id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id', 'id');
    }
}
