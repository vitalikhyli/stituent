<?php

namespace App\Models\Pilot;

use App\Entity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PilotItem extends Model
{
    protected $table = 'pilot_items';

    public function getFiscalYearAttribute()
    {
        $year = Carbon::parse($this->date)->format('Y');

        if (Carbon::parse($this->date)->format('m') > 6) {
            $year += 1;
        }

        echo 'FY'.$year;
    }

    public function program()
    {
        return $this->hasOne(PilotProgram::class, 'id', 'program_id');
    }

    // public function beneficiaries() {
    //     return $this->belongsToMany(PilotBeneficiary::class, 'pilot_beneficiary_item', 'item_id', 'beneficiary_id');
    // }

    public function beneficiaries()
    {
        return $this->belongsToMany(Entity::class, 'pilot_beneficiary_item', 'item_id', 'entity_id');
    }

    public function partners()
    {
        return $this->belongsToMany(Entity::class, 'pilot_item_partner', 'item_id', 'entity_id');
    }
}
