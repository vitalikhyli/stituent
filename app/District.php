<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $connection = 'main';

    public function voterCount($count_record, $index = null)
    {
        if ($this->type == 'F') $count_record_field = 'congress_districts';
        if ($this->type == 'H') $count_record_field = 'house_districts';
        if ($this->type == 'S') $count_record_field = 'senate_districts';

        if ($index) return $count_record->{$count_record_field}[$index][$this->code];

        return $count_record->{$count_record_field}['voters'][$this->code];
    }

    public function getFullNameAttribute()
    {
        $name = $this->name;
        if ($this->type == 'F') {
            $type = 'Congress';
        } else {
            $type = $this->type;
        }

        return $type.' - '.$name.' ('.$this->elected_official_name.')';
    }

    public function getSearchableNameAttribute()
    {
        // No commas
        return preg_replace("/[^A-Za-z0-9?!\s\&]/", '', $this->name);
    }

    public function getElectedOfficialPartyShortAttribute()
    {
        return substr($this->elected_official_party, 0, 1);
    }
}
