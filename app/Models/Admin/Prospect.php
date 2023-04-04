<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    protected $table = 'account_prospects';

    public function city()
    {
        return $this->hasOne(\App\Municipality::class, 'code', 'city_code');
    }
}
