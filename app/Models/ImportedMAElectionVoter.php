<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedMAElectionVoter extends Model
{
    // This is the model for uploading the election_voter data
    // from that vendor

    protected $connection = 'voters';

    protected $table = 'i_ma_election_voter_import';

    public $timestamps = false;
    

    public function district()
    {
    	return $this->belongsTo(ImportedMAElection::class, 'election_id');
    }

}
