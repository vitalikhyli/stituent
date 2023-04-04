<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SalesTeam extends Model
{
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
