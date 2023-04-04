<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCVoterNote extends Model
{
    protected $primaryKey = 'noteID';
    protected $table = 'cms_voter_notes';
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
