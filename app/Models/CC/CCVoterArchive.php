<?php

namespace App\Models\CC;

use App\VoterMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class CCVoterArchive extends CCVoter
{
    protected $table = 'cms_voters_archive';
}
