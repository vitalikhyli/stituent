<?php

namespace App\Models\Pilot;

use Illuminate\Database\Eloquent\Model;

class PilotProgram extends Model
{
    protected $table = 'pilot_programs';

    public function items()
    {
        return $this->hasMany(PilotItem::class, 'program_id', 'id');
    }
}
