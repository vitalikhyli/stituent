<?php

namespace App\Models\CC;

use App\Person;
use App\VoterMaster;
use DB;
use Illuminate\Database\Eloquent\Model;

class CCBulkEmailVoter extends Model
{
    protected $primaryKey = 'cms_voter_bulkemail_id';
    protected $table = 'cms_voter_bulkemail';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function voter()
    {
        return $this->belongsTo(VoterMaster::class, 'cms_voter_bulkemail_voterID', 'import_order');
    }

    public function person()
    {
        return $this->belongsTo(Person::class, 'cms_voter_bulkemail_voterID', 'old_cc_id');
    }
}
