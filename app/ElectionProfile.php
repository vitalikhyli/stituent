<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ElectionProfile extends Model
{
    protected $connection = 'voters';

    public function voter()
    {
        return $this->belongsTo(Voter::class);
    }

    public function getTable()
    {
        return session('team_state').'_election_profiles';
    }
}
