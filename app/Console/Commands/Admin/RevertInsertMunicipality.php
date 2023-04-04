<?php

namespace App\Console\Commands\Admin;

use App\Import;
use App\VoterImport;
use App\VoterMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class RevertInsertMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:revert_insert_municipality {--import_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $import_id = $this->option('import_id');
        if (! $import_id) {
            echo "Needs import_id like cf:insert_municipality --import_id=\n";

            return;
        }

        $import = Import::find($import_id);
        $import->reverting = true;
        $import->save();

        ////////////////////////////////////////////////////////////////////////////
        //
        // DELETE FROM MASTER -- Records that had been added
        //

        if (! $import->municipality_id) {
            echo 'City bad in import!';
            exit;
        }
        if (! $import->started_at) {
            echo 'Started_at date/time bad in import!';
            exit;
        }

        $records = VoterMaster::where('created_at', '>=', $import->started_at)
                              ->where('city_code', $import->municipality_id)
                              ->get();

        $r = 0;
        foreach ($records as $this_was_added) {
            $this_was_added->forceDelete();
            $r++;
            echo 'Removing from Master: '.$r."\r           ";

            if ($import->new_count - 1 > 0) {
                $import->new_count--;
            }
            $import->save();
        }
        echo 'Removed from Master: '.$r."\r\n";

        ////////////////////////////////////////////////////////////////////////////
        //
        // UPDATE MASTER -- Records that had been replaced
        //

        $at_a_time = 100;
        $i = 0;
        $c = 0;

        session(['import_table' => $import->table_name.'_replaced']);
        $old_records_count = VoterImport::count();

        while ($c < $old_records_count) {
            $old_records = VoterImport::skip($at_a_time * $i)
                                      ->take($at_a_time)
                                      ->get();

            foreach ($old_records as $old_record) {
                $this_was_updated = VoterMaster::find($old_record->id);
                if ($this_was_updated) {
                    foreach (collect($this_was_updated)->keys() as $field) {
                        $this_was_updated->$field = $old_record->$field;
                    }
                    $this_was_updated->save();
                }

                if ($import->updated_count - 1 > 0) {
                    $import->updated_count--;
                }
                $import->save();
            }

            $c += $old_records->count();
            echo 'Reverting: '.$c."\r\n";
        }

        ////////////////////////////////////////////////////////////////////////////
        //
        // TRUNCATE _changed + _replaced?
        //
        // session(['import_table' => $import->table_name.'_changed']);
        // VoterImport::truncate();

        $import->completed_at = null;
        $import->new_count = 0;
        $import->updated_count = 0;
        $import->changed_count = 0;
        $import->reverting = false;
        $import->save();
    }
}
