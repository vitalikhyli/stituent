<?php

namespace App\Models\Pilot;

use Illuminate\Database\Eloquent\Model;

class PilotReport extends Model
{
    protected $table = 'pilot_reports';

    public function getHeadlineAttribute($headline)
    {
        return "Northeastern's Commitment to Boston Goes Far Beyond Voluntary Payments";
    }
}
