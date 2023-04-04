<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;


class VoterMaster extends Voter
{
    protected $connection = 'voters';

    public function getTable()
    {
        if (session('team_state')) {
            return 'x_voters_'.session('team_state').'_master';
        }
        return 'x_voters_MA_master';
    }

    public function massGis()
    {
        return $this->belongsTo(MassGIS::class, 'mass_gis_id');
    }

    public function getAgeAttribute()
    {
        if ($this->dob) {
            return \Carbon\Carbon::parse($this->dob)->age;
        }
    }
    // public function importedMAElections()
    // {
    //     return $this->hasMany(Models\ImportedMAElectionVoter::class, 'id', 'cf_voter_id');
    // }
}
