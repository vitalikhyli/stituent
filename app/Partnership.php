<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partnership extends Model
{
    use SoftDeletes;
    
    protected $casts = ['contacts' => 'array', 'data' => 'array'];

    public function department()
    {
        return $this->belongsTo(Entity::class, 'department_id');
    }

    public function partner()
    {
        return $this->belongsTo(Entity::class, 'partner_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'partner_id');
    }

    public function partnershipType()
    {
        return $this->belongsTo(PartnershipType::class);
    }
}
