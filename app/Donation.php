<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use SoftDeletes;

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDateFormattedAttribute()
    {
        return Carbon::parse($this->date)->format('d M Y');
    }

    public function getOccupationEmployerAttribute()
    {
        if (! $this->occupation && ! $this->employer) {
            return null;
        }

        return $this->occupation.' / '.$this->employer;
    }

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
        return $this->belongsTo(Participant::class);
    }

    public function save(array $options = [])
    {
        if (!$this->id) {
            addActionFromObject($this, 'Contributed', $this->amount, null);
        }
        return parent::save($options);
    }
}
