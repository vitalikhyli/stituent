<?php

namespace App\Console\Commands\Admin\UploadToMaster;

use App\Import;
use App\VoterImport;
use App\VoterMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InsertMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:insert_municipality {--import_id=}';

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

        $small_count = DB::connection('imports')->table($import->table_name)->count();

        $stats = ['import_count'    => $small_count,
                  'new'             => 0,
                  'replaced'        => 0,
                  'changed'         => 0,
                 ];

        $at_a_time = 100;
        $i = 0;
        $c = 0;

        $old_records_table = str_replace('-', '_', $import->table_name.'_replaced');
        DB::connection('imports')->select('CREATE TABLE IF NOT EXISTS '.$old_records_table.' LIKE '.$import->table_name);

        $what_changed_table = str_replace('-', '_', $import->table_name.'_changed');
        DB::connection('imports')->select('CREATE TABLE IF NOT EXISTS '.$what_changed_table.' LIKE '.$import->table_name);
        // DB::connection('imports')->select("CREATE TABLE IF NOT EXISTS ".$what_changed_table." (id varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (id))");

        while ($c < $stats['import_count']) {
            session(['import_table' => $import->table_name]);
            $records = VoterImport::skip($at_a_time * $i)
                         ->take($at_a_time)
                         ->get();

            foreach ($records as $new) {
                unset($new->voter_id);

                $existing = VoterMaster::find($new->id);

                if (! $existing) {

                    // This is a new record not in the Master file

                    $stats['new']++;
                    $result = new VoterMaster;
                    foreach ($new->toArray() as $index => $val) {
                        $result->$index = $val;
                    }
                    $result->original_import = $new->toArray();
                    unset($result->voter_id);
                    try {
                        $result->save();
                    } catch (\Exception $e) {
                        dd($result, $new, $e->getMessage());
                    }
                } else {

                    // Copy Old from Master file

                    session(['import_table' => $old_records_table]);
                    if (! VoterImport::find($new->id)) {
                        $vi = new VoterImport;
                        foreach ($existing->getAttributes() as $attr => $val) {
                            $vi->$attr = $val;
                        }
                        unset($vi->voter_id);
                        try {
                            $vi->save();
                        } catch (\Exception $e) {
                            dd($e->getMessage(), $vi);
                        }
                    }

                    // Update / Replace Master with New

                    $any_diff = false;

                    $fields_to_insert = collect($new)->keys();

                    // Do not insert fields that were not present in New file:

                    $fields_to_omit = [];
                    foreach ($import->column_map as $db_field => $file_field) {
                        if (! $file_field) {                         // i.e. not matched to a field
                            $db_field = explode('_', $db_field)[1]; // e.g. 01_field prefix
                            $fields_to_omit[] = $db_field;
                        }
                    }
                    $fields_to_insert = $fields_to_insert->diff($fields_to_omit);

                    // Specific field logic:
                    foreach ($fields_to_insert as $field) {

                        // Neer insert created_at
                        if ($field == 'created_at') {
                            continue;
                        }

                        // New fields that are null, but the same field in Master is not:
                        if ((! $new->$field || $new->$field == '') && $existing->$field) {

                            // Never null gender
                            if ($field == 'gender') {
                                continue;
                            }

                            // Null certain address info only if address has changed
                            if ($field == 'address_apt' ||
                                $field = 'ward' ||
                                $field = 'precinct'
                                ) {
                                if ($new->address_street == $existing->address_apt) {
                                    continue;
                                }
                            }

                            //Never null district IDs
                            if ($field == 'governor_district') {
                                continue;
                            }
                            if ($field == 'congress_district') {
                                continue;
                            }
                            if ($field == 'senate_district') {
                                continue;
                            }
                            if ($field == 'house_district') {
                                continue;
                            }
                        }

                        // Has anything really changed?

                        if ($field == 'updated_at' || $field == 'created_at') {

                            // Skip -- Do not compare these
                        } elseif ($field == 'dob' || $field == 'registration_date') {

                            // Compare dates

                            if (Carbon::parse($existing->$field)->toDateString() != Carbon::parse($new->$field)->toDateString()) {
                                $any_diff = true;
                            }
                        } else {

                            // Compare other fields

                            if ($existing->$field != $new->$field) {
                                $any_diff = true;
                                // echo $field." -- ".$existing->$field." ".$new->$field."\r\n";
                            }
                        }

                        $existing->$field = $new->$field;
                    }
                    unset($existing->voter_id);
                    $existing->save();

                    $stats['replaced']++;

                    if ($any_diff) {
                        $stats['changed']++;

                        // CAUSING QUEUE PROBLEMS?
                        $changed = DB::connection('imports')->table($what_changed_table)
                                                    ->where('id', $existing->id)
                                                    ->first();
                        if (! $changed) {
                            DB::connection('imports')->table($what_changed_table)
                                                     ->insert(['id' => $existing->id,
                                                               'location' => DB::raw("POINT(0,0)")]);
                        }

                        // ALTERNATE WAY?
                        // session(['import_table' => $what_changed_table]);
                        // if (!VoterImport::find($existing->id)) {
                        //     $this_changed = new VoterImport;
                        //     $this_changed->id = $existing->id;
                        //     $this_changed->save();
                        // }
                    }
                }
            }
            $import->new_count = $stats['new'];
            $import->updated_count = $stats['replaced'];
            $import->changed_count = $stats['changed'];
            $import->save();

            $c += $records->count();
            $i++;
            echo 'Record: '.$c."\r\n";
        }

        $import->completed_at = Carbon::now();
        $import->save();
    }
}
