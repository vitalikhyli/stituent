<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class VoterImport extends Voter
{
    protected $connection = 'imports';

    public function getTable()
    {
        return session('import_table');
    }
}
