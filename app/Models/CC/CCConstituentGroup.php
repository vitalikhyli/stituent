<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCConstituentGroup extends Model
{
    protected $primaryKey = 'groupID';
    protected $table = 'cms_voter_groups';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function assignments()
    {
        return $this->hasMany(CCGroupAssignment::class, 'groupID', 'groupID');
    }
}
