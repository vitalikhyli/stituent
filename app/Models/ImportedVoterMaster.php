<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\VoterMaster;


class ImportedVoterMaster extends VoterMaster
{
    protected $connection = 'voters';

    public function getTable()
    {
        if (session('table_while_importing_master')) {
            return session('table_while_importing_master');
        }
    }

    public function importedMAElections()
    {
        return $this->hasMany(ImportedMAElectionVoter::class, 'id', 'cf_voter_id');
    }  
    public function sameStreet()
    {
        return ImportedVoterMaster::where('address_city', $this->address_city)
                                  ->where('address_street', $this->address_street)
                                  ->take(50)
                                  ->inRandomOrder();
    }  

}
