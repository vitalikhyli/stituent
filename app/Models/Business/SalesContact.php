<?php

namespace App\Models\Business;

use Illuminate\Database\Eloquent\Model;

class SalesContact extends Model
{
    protected $table = 'sales_contacts';

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }
}
