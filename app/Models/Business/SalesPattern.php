<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SalesPattern extends Model
{
    protected $table = 'sales_patterns';

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function steps()
    {
        return $this->hasMany(\App\Models\Business\SalesStep::class, 'pattern_id');
    }
}
