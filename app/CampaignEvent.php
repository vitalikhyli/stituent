<?php

namespace App;

use App\CampaignEventInvite;
use App\CampaignEventNote;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignEvent extends Model
{
    use SoftDeletes;

    protected $table = 'campaign_events';

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }
    public function campaignEventNotes()
    {
        //dd("Laz");
        return $this->hasMany(CampaignEventNote::class);
    }

    public function getTotalAttendingAttribute()
    {
        return $this->invitees()->where('can_attend', true)->sum('guests')
               + $this->invitees()->where('can_attend', true)->count();
    }

    public function isInvitee($participant_id)
    {
        return (CampaignEventInvite::where('campaign_event_id', $this->id)->where('participant_id', $participant_id)->exists()) ? true : false;
    }

    public function invitees()
    {
        return $this->belongsToMany(Participant::class,
                                     'campaign_event_invites',
                                     'campaign_event_id',
                                     'participant_id')
        ->withPivot('can_attend', 'guests', 'comped');
    }


    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->format('d M Y');
    }

    public function donations()
    {
        return $this->hasMany(Donation::class, 'campaign_event_id', 'id');
    }

    public function getFullVenueAttribute()
    {
        $string = $this->venue_name;
        if ($this->venue_city) {
            $string .= ' - '.$this->venue_city;
        }
        if ($this->venue_state) {
            $string .= ', '.$this->venue_state;
        }

        return $string;
    }
}
