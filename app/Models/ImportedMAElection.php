<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedMAElection extends Model
{
    // This is the model for uploading the election_voter data
    // from that vendor

    protected $connection = 'voters';

    protected $table = 'i_ma_elections_import';

}
