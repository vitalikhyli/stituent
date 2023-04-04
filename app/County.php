<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class County extends Model
{

    public function voterCount($count_record, $index = null)
    {
    	if ($index) return $count_record->counties[$index][$this->code];
        return $count_record->counties['voters'][$this->code];
    }

    public function voters()
    {
        return $this->hasMany(Voter::class, 'county_code', 'code');
    }

}
