<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupEntity extends Model
{
    protected $table = 'group_entity';

    protected $casts = ['data' => 'array'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }
}
