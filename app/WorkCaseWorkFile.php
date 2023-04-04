<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkCaseWorkFile extends Pivot
{
    use SoftDeletes;

    public $table = 'case_file';

    public function case()
    {
        return $this->belongsTo(WorkCase::class); // SOFT DELETES
    }
}
