<?php

namespace App\Models\Campaign;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpportunityVolunteer extends Model
{
    use HasFactory;

    protected $table = 'opportunity_volunteer';

    public function __construct($opp = null, $vol = null)
    {
    	if ($opp && $vol) {
    		$this->opportunity_id = $opp->id;
    		$this->volunteer_id = $vol->id;
    	}
    }
}
