<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ContactPerson extends Pivot
{
    public $table = 'contact_person';
}
