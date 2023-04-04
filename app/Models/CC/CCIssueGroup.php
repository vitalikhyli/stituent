<?php

namespace App\Models\CC;

use DB;
use Illuminate\Database\Eloquent\Model;

class CCIssueGroup extends Model
{
    protected $primaryKey = 'categoryID';
    protected $table = 'cms_issue_categories';
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
        return $this->hasMany(CCIssueGroupAssignment::class, 'categoryID', 'categoryID');
    }
}
