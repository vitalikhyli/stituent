<?php

namespace App\Models\Admin;

use App\Models\Admin\DataImport;
use Illuminate\Database\Eloquent\Model;

class DataJob extends Model
{
    protected $table = 'data_jobs';

    public function import()
    {
        return $this->belongsTo(DataImport::class, 'data_import_id'); //This needed a different key col
    }

    public function rollback()
    {
        $this->done = 0;
        $this->remaining = null;
        $this->start = null;
        $this->end = null;
        $this->duration = null;
        $this->count = null;
        $this->rate = null;
        $this->save();
    }

    public function add($type = null, $import_id = null, $arguments = null)
    {
        $this->type = $type;
        $this->data_import_id = $import_id;
        if ($arguments) {
            $this->arguments = json_encode($arguments);
        }
        $this->save();
    }

    public function start()
    {
        $start = intval(microtime(true) * 1000);
        $this->start = $start;
        $this->save();
    }

    public function markAsDone()
    {
        $this->done = 1;
        $end = intval(microtime(true) * 1000);
        $this->end = $end;
        $this->duration = $end - $this->start;

        $count = 0;

        if (($this->type == 'import') ||
            ($this->type == 'enrich') ||
            ($this->type == 'populateSlice') ||
            ($this->type == 'createHouseholds') ||
            ($this->type == 'createHouseholdsBySlice')
        ) {
            $count = $this->import->count;
        }

        $this->count = $count;
        $this->rate = round($count / $this->duration * 1000);
        $this->save();
    }
}
