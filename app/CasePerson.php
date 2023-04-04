<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CasePerson extends Pivot
{
    public $table = 'case_person';

    public function person()
    {
    	return $this->belongsTo(Person::class);
    }
}
