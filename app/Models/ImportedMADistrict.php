<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ImportedMADistrict extends Model
{
    // This is the model for uploading the district data
    // from that vendor

    protected $connection = 'voters';

    protected $table = 'i_ma_districts_import';
}
