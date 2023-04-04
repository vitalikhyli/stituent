<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedMAResult extends Model
{
    protected $connection = 'voters';
    protected $table = 'i_ma_results_import';

    protected $casts = ['original_import' => 'array'];
}
