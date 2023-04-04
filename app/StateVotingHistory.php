<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StateVotingHistory extends Model
{
    protected $primaryKey = 'Voter ID Number';
    public $incrementing = false;
    public $timestamps = false;
    protected $table = 'MA_STATE_HISTORY_IMPORTS';
    protected $connection = 'voters';
    protected $dates = ['Election Date'];

    public function voter()
    {
        return $this->belongsTo(VoterMaster::class, 'Voter ID Number');
    }
}
