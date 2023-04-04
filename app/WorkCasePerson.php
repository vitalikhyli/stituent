<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkCasePerson extends Pivot
{
    use SoftDeletes;

    public $table = 'case_person';

    protected $casts = [
        'data' 			=> 'array',
    ];

    public function case()
    {
        return $this->belongsTo(WorkCase::class); // SOFT DELETES
    }
}
