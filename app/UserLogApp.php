<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserLogApp extends Model
{
    protected $casts = ['debug' => 'array'];
}
