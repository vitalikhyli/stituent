<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CCElection extends Model
{
    protected $primaryKey = 'keyID';
    protected $table = 'cms_election_data';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function getCodeAttribute()
    {
        // MA-2016-11-08-STATE0000000000-329-U-0
        $state = $this->voter_state;
        $date = $this->election_date;
        $type = str_pad($this->election_type, 5, '0');
        $city = str_pad($this->election_city_code, 4, '0', STR_PAD_LEFT);
        if (! Str::startsWith($type, 'L')) {
            $city = '0000';
        }

        return "$state-$date-$type-$city";
    }

    public function getVoterInfoAttribute()
    {
        // 0001-R-0 ????
        $city = str_pad($this->election_city_code, 4, '0', STR_PAD_LEFT);
        $party = $this->election_party;
        $ballot = $this->election_party_ballot;
        if (! $ballot) {
            $ballot = 0;
        }

        return $city.'-'.$party.'-'.$ballot;
    }
}
