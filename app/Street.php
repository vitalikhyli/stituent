<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Street extends Model
{
	protected $primaryKey = 'id';
	protected $connection = 'voters';
    protected $table = 'x_MA_streets';
    public $incrementing = false;

    public function voters()
    {
    	return VoterMaster::where('household_id', 'LIKE', $this->id."%");
    }
}
