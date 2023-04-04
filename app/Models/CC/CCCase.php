<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCCase extends Model
{
    protected $primaryKey = 'contact_issueID';
    protected $table = 'cms_contact_issues';
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
