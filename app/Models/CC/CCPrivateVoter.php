<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCPrivateVoter extends Model
{
    protected $primaryKey = 'keyID';
    protected $table = 'cms_voter_private';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function ccVoter()
    {
        return $this->belongsTo(CCVoter::class, 'voterID', 'voterID');
    }
}
