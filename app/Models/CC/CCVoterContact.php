<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCVoterContact extends Model
{
    protected $primaryKey = 'contactID';
    protected $table = 'cms_voter_contact';
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
