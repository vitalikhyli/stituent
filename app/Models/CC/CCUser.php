<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCUser extends Model
{
    protected $primaryKey = 'userID';
    protected $table = 'cms_users';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }
}
