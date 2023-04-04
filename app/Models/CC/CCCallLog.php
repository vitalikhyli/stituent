<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCCallLog extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'call_logs';

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function ccUser()
    {
        return $this->belongsTo(CCUser::class, 'user_id', 'userID');
    }
}
