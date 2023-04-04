<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VoterSlice extends Model
{
    protected $table = 'voter_slices';

    protected $casts = [
            'birthdays'  => 'array',
    ];

    public function getStateAttribute()
    {
    	return substr($this->name, 2, 2);
    }

    public function countRecord()
    {
        return $this->hasOne(VoterSliceCount::class, 'slice_id', 'id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'db_slice', 'name');
    }
}
