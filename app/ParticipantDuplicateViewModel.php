<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Participant;
use DB;

class ParticipantDuplicateViewModel extends Model
{
    use HasFactory;

    public $duplicate_voter_ids;

    public function __construct()
    {
        $ids = Participant::thisTeam()
                          ->whereNotNull('voter_id')
                          ->groupBy('voter_id')
                          ->having(DB::raw('count(voter_id)'), '>', 1)
                          ->pluck('voter_id');

        $this->duplicate_voter_ids = Participant::thisTeam()
                                                ->whereIn('voter_id', $ids)
                                                ->get()
                                                ->groupBy('voter_id');
    }

    
}
