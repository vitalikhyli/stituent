<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Import extends Model
{
    use SoftDeletes;

    protected $connection = 'imports';
    protected $casts = ['column_map' => 'array'];
    protected $dates = ['created_at', 'updated_at', 'imported_at', 'completed_at', 'started_at'];

    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }

    public function getNameAttribute()
    {
    	if ($this->municipality) {
    		return $this->municipality->name;
    	}
    }
}
