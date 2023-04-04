<?php

namespace App;

use App\Group;
use App\User;
use Auth;
// use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

// use App\Traits\RecordPivotSignature;

class GroupPerson extends Pivot
// class GroupPerson extends Model
{
    // use RecordPivotSignature;

    public $table = 'group_person';

    public $incrementing = true;

    protected $casts = [
        'data' 			=> 'array',
    ];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class); // SOFT DELETES
    }

    public function getCreatedAtReadableAttribute()
    {
        return $this->created_at->format('n/j/Y');
    }

    public function getCreatedByNameAttribute()
    {
        $user = User::find($this->created_by);
        return ($user) ? $user->short_name : null;
    }
}
