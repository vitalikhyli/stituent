<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCBulkEmail extends Model
{
    protected $primaryKey = 'cms_bulkemail_tracker_id';
    protected $table = 'cms_bulkemail_tracker';
    public $timestamps = false;

    public function getConnection()
    {
        if (env('APP_ENV') == 'production') {
            return DB::connection('cc_local');
        }

        return DB::connection('cc_remote');
    }

    public function ccSender()
    {
        return $this->belongsTo(CCUser::class, 'cms_bulkemail_tracker_sent_by_login', 'login');
    }

    public function ccBulkEmailVoters()
    {
        return $this->hasMany(CCBulkEmailVoter::class, 'cms_voter_bulkemail_tracker_id', 'cms_bulkemail_tracker_id');
    }
}
