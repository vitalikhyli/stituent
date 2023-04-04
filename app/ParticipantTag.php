<?php

namespace App;

use Auth;
use Illuminate\Database\Eloquent\Model;

class ParticipantTag extends Model
{
    protected $table = 'participant_tag';

    public function scopeThisTeam($query)
    {
        return $query->where('team_id', Auth::user()->team->id);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function save(array $options = [])
    {   
        if (!$this->id) {
            addActionFromObject($this, 'Tagged', $this->tag->name, null);
        }
        return parent::save($options);
    }
}
