<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function hasParticipant($participant)
    {
        if (!$participant) {
            return false;
        }
        $ids = $this->participants()->pluck('participants.id');
        //dd($ids);
        return $ids->contains($participant->id);
    }
}
