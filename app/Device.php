<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Device extends Model
{
	use SoftDeletes;

	protected $dates = [
		'created_at',
		'updated_at',
		'live_at'
	];
	
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

}
