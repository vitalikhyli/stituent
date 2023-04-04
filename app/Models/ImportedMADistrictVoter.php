<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedMADistrictVoter extends Model
{
    // This is the model for uploading the district_voter data
    // from that vendor

    protected $connection = 'voters';

    protected $table = 'i_ma_district_voter_import';

    public function district()
    {
    	return $this->belongsTo(ImportedMADistrict::class, 'district_id');
    }

}

