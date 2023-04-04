<?php

namespace App\Models\Admin;

use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use App\Team;
use App\Voter;
use App\VotingHousehold;
use Artisan;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Schema;

function fourZeros($n)
{
    return str_pad($n, 4, '0', STR_PAD_LEFT);
}

class DataImport extends Model
{
    //////////////////////////////////////////////////////////////////////////////////
    //
    //	OPERATIONAL
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function __construct($type = null, $team_id = null, $name = null)
    {
        if ($type) {
            $this->type = $type;
        }
        if ($team_id) {
            $this->team_id = $team_id;
        }
        if ($name) {
            $this->name = $name;
        }
        if ($team_id) {
            $this->data_folder_id = Team::find($team_id)->data_folder_id;
        }
    }

    public function add($type = null, $team_id = null, $name = null)
    {
        $this->type = $type;
        $this->team_id = $team_id;
        $this->name = $name;
        $this->save();

        return $this;
    }

    public function jobs()
    {
        return $this->hasMany(DataJob::class);
    }

    public function getTableDeployAttribute()
    {
        if ($this->type == 'v') {
            return 'x_voters_'.fourZeros($this->team_id);
        }
        if ($this->type == 'hh') {
            return 'x_households_'.fourZeros($this->team_id);
        }
    }

    public function getTableBenchAttribute()
    {
        return 'x_'.$this->slug;
    }

    public function getActiveTableAttribute()
    {
        if ($this->deployed == 1) {
            return $this->table_deploy;	//Table is deployed
        } else {
            return $this->table_bench;	//Table is on the bench
        }
    }

    public function setGroupSlug()
    {
        $max = DB::select('select max(version) as theversion from data_imports where team_id='.$this->team_id);

        $n = collect($max)->first()->theversion;
        $this->version = fourZeros($n + 1);
        ($this->type != 'v') ? $suffix = '_'.$this->type : $suffix = '';
        $this->slug = fourZeros($this->team_id).'_'.fourZeros($this->version).$suffix;
        $this->save();
    }

    public function realCount()
    {
        $c = DB::table($this->active_table)->count();
        $this->count = $c;
        $this->save();

        return $c;
    }

    public function relatedHouseholds()
    {
        return self::where('version', $this->version)
                         ->where('team_id', $this->team_id)
                         ->where('type', 'hh')
                         ->first();
    }

    public function relatedVoters()
    {
        return self::where('version', $this->version)
                         ->where('team_id', $this->team_id)
                         ->where('type', 'v')
                         ->first();
    }

    public function sliceOf()
    {
        return self::where('id', $this->slice_of_id)
                         ->first();
    }

    public function jobReport()
    {
        $jobs = DataJob::where('data_import_id', $this->id)->where('done', 0)->count();
        $thejob = DataJob::where('data_import_id', $this->id)->where('done', 0)->limit(1)->first();
        $msg = $jobs.' jobs left <i class="fas fa-arrow-right"></i> '.$thejob->type.' <i class="fas fa-arrow-right"></i> '.number_format($thejob->remaining, 0, '.', ',').' remaining';

        return $msg;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	INDEX HANDLING
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function restoreIndexes($exceptions = null)
    {
        if ($exceptions == null) {
            $exceptions = [];
        }

        Schema::table($this->table_bench, function (Blueprint $table) use ($exceptions) {
            if ($this->type == 'v') {
                $keys = collect(DB::select('SHOW INDEXES FROM x__template_voters'));
            }
            if ($this->type == 'hh') {
                $keys = collect(DB::select('SHOW INDEXES FROM x__template_households'));
            }

            foreach ($keys as $thekey) {
                if (! in_array($thekey->Key_name, $exceptions)) {
                    if ($thekey->Key_name == 'PRIMARY') {
                        $table->primary($thekey->Column_name);
                    } elseif ($thekey->Non_unique == 0) {
                        $table->unique($thekey->Column_name);
                    } else {
                        $table->index($thekey->Column_name);
                    }
                }
            }
        });
    }

    public function dropIndexes($exceptions = null)
    {
        if ($exceptions == null) {
            $exceptions = [];
        }

        Schema::table($this->table_bench, function (Blueprint $table) use ($exceptions) {
            $keys = collect(DB::select('SHOW INDEXES FROM '.$this->table_bench));
            foreach ($keys as $thekey) {
                if (! in_array($thekey->Key_name, $exceptions)) {
                    if ($thekey->Key_name == 'PRIMARY') {
                        $table->dropPrimary('PRIMARY');
                    } elseif ($thekey->Non_unique == 0) {
                        $table->dropUnique($thekey->Key_name);
                    } else {
                        $table->dropIndex($thekey->Key_name);
                    }
                }
            }
        });
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	RENAMING
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function archive()
    {
        if ($this->deployed) {
            dd('Error - Cannot archive a table that is deployed');
        } elseif (! env('DB_ARCHIVE_DATABASE')) {
            dd('Error - Archive database connection (should be called "archive") is not set up.');
        } else {
            Schema::connection('archive')->dropIfExists($this->table_bench);

            DB::statement('RENAME TABLE '.env('DB_DATABASE').'.'.$this->table_bench.' TO '.env('DB_ARCHIVE_DATABASE').'.'.$this->table_bench);

            $this->archived = 1;
            $this->save();
        }
    }

    public function deploy()
    {
        $team_id = $this->team_id;

        $current = self::where('team_id', $team_id)
                             ->where('type', 'v')
                             ->where('deployed', 1);

        if ($current->exists()) {
            $existing = self::where('team_id', $team_id)
                                 ->where('type', 'v')
                                 ->where('deployed', 1)
                                 ->first();

            DB::statement('RENAME TABLE '.$existing->table_deploy.' TO '.$existing->table_bench);

            $existing->deployed = 0;
            $existing->save();
        }

        $new_voter_file = $this->table_deploy;

        //Just in case a table named this was already set up in the seeder:
        if (Schema::hasTable($new_voter_file)) {
            DB::statement('RENAME TABLE '.$new_voter_file.' TO z_old_'.$new_voter_file);
        }

        DB::statement('RENAME TABLE '.$this->table_bench.' TO '.$new_voter_file);

        $this->deployed = 1;
        $this->save();

        return 0;
    }

    public function deployHouseholds()
    {
        $current = self::where('team_id', $this->team_id)
                             ->where('type', 'hh')
                             ->where('deployed', 1);

        if ($current->exists()) {
            $existing = self::where('team_id', $this->team_id)
                                 ->where('type', 'hh')
                                 ->where('deployed', 1)
                                 ->first();

            DB::statement('RENAME TABLE '.$existing->table_deploy.' TO '.$existing->table_bench);

            $existing->deployed = 0;
            $existing->save();
        }

        $current = self::where('team_id', $this->team_id)
                             ->where('type', 'hh')
                             ->where('version', $this->version)
                             ->first();

        //Just in case a table named this was already set up in the seeder:
        if (Schema::hasTable($current->table_deploy)) {
            DB::statement('RENAME TABLE '.$current->table_deploy.' TO z_old_'.$current->table_deploy);
        }

        DB::statement('RENAME TABLE '.$current->table_bench.' TO '.$current->table_deploy);
        $current->deployed = 1;
        $current->save();

        return 0;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	BASIC DATA MANIPULATION
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function rollback($destroy_table = null, $destroy_import = null)
    {
        $this->count_expected = null;
        $this->count_pointer = null;
        $this->save();

        if ($destroy_table) {
            Schema::dropIfExists($this->active_table);
        }

        if ($destroy_import) {
            $this->delete();
        }
    }

    public function isReady()
    {
        $this->ready = 1;
        $this->save();
        if ($this->type == 'v') {
            $this->relatedHouseholds()->isReady();
        }

        return 0;
    }

    public function isNotReady()
    {
        $this->ready = 0;
        $this->save();
        if (
            ($this->type == 'v') && ($this->relatedHouseholds())
        ) {
            $this->relatedHouseholds()->isReady();
        }

        return 0;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	SLICES
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function populateSliceHouseholds($arguments)
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//

        $voters = self::find($arguments['voters']);

        //-----------------------------------[ SET UP ]---------------------------------//

        if (! $this->count) {
        }

        //-----------------------------------[ LOOP ]-----------------------------------//

        try {
            $rows = DB::table($voters->name)->skip($this->count)->take(100)->get();

            $the_row_count = $rows->count();
            if ($the_row_count == 0) {
                $remaining = 0;
            } else {
                $remaining = $the_row_count + $this->count; //COUNT UP
            }

            foreach ($rows as $therow) {
                if (DB::table($this->name)->where('id', $therow->household_id)->doesntExist()) {
                    DB::table($this->name)->insert(
                        ['id' => $therow->household_id]
                    );
                }
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        // echo "\r\n".$this->name." ".$this->count."\r\n";

        $this->count += 100;
        $this->save();

        //---------------------------------[ SHUTDOWN ]---------------------------------//

        if ($remaining <= 0) {
            $remaining = 0;
            // $this->count 	= 0;
            // $this->save();
        }

        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        return $remaining;
    }

    public function populateTableWithSliceOfMaster($arguments)
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//

        $db_land = env('DB_VOTER_DATABASE');

        if ($arguments['slice_sql']) {
            $sql = ' WHERE '.$arguments['slice_sql'];
        }

        //-----------------------------------[ SET UP ]---------------------------------//

        $chunksize = 10000;

        if (! $this->count_expected) {

            // echo "Counting total...\r\n";

            $count = DB::select('select count(*) as c from '.$db_land.'.x_voters_MA_master'.$sql);
            // dd('select count(*) as c from '.$db_land.".x_voters_MA_master".$sql);

            $this->count_expected = collect($count)->first()->c;
            $this->count = 0;

            $this->save();
            // $this->setGroupSlug();
        }

        //-----------------------------------[ LOOP ]-----------------------------------//

        $limit_offset = ' LIMIT '.$chunksize.' OFFSET '.$this->count;

        try {
            DB::statement('REPLACE '.$this->name.' SELECT * FROM '.$db_land.'.x_voters_MA_master'.$sql.$limit_offset);
        } catch (\Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }

        $this->count = $this->count + $chunksize;
        $this->save();
        $remaining = $this->count_expected - $this->count;

        //---------------------------------[ SHUTDOWN ]---------------------------------//

        if ($remaining <= 0) {
            $remaining = 0;
            // echo "\r\n";
        }

        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        return $remaining;
    }

    public function moveSlicePointers()
    {
        $original_slices = self::where('slice_of_id', $this->parent_id)->get();
        foreach ($original_slices as $theslice) {
            $theslice->slice_of_id = $this->id;
            $theslice->save();
        }
    }

    public function defineSlice($arguments, $name = null)
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//
        //-----------------------------------[ SET UP ]---------------------------------//
        //-----------------------------------[ LOOP ]-----------------------------------//
        //---------------------------------[ SHUTDOWN ]---------------------------------//
        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        $slice_of_id = $arguments['slice_of_id'];
        $slice_sql = $arguments['slice_sql'];

        $pie = self::find($slice_of_id);

        if ($slice_sql) {
            $sql = ' WHERE '.$slice_sql;
        }
        $count = DB::select('select count(*) as c from '.$pie->active_table.$sql);
        $count_expected = collect($count)->first()->c;
        $this->count_expected = $count_expected;

        $this->count = 0;

        $this->slice_of_id = $slice_of_id;
        $this->slice_sql = $slice_sql;
        if (! $this->name) {
            if (! $name) {
                $this->name = 'Slice of '.$pie->name;
            } else {
                $this->name = $name;
            }
        }

        $this->save();

        $this->setGroupSlug();

        DB::statement('CREATE TABLE '.$this->table_bench.' LIKE '.$pie->active_table);

        return 0;
    }

    public function populateSlice($update_only = null)
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//
        //-----------------------------------[ SET UP ]---------------------------------//
        //-----------------------------------[ LOOP ]-----------------------------------//
        //---------------------------------[ SHUTDOWN ]---------------------------------//
        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        $sql = $this->slice_sql;
        if ($sql) {
            $sql = ' WHERE '.$sql;
        }

        $pie = self::find($this->slice_of_id);

        $limit_offset = ' LIMIT 100 OFFSET '.$this->count;

        // Somehow screws up again tries to do some twice? -->use INSERT IGNORE or REPLACE

        DB::statement('REPLACE '.$this->table_bench.' SELECT * FROM '.$pie->active_table.$sql.$limit_offset);

        $this->count = $this->count + 100;
        $this->enriched = 1;
        $this->save();

        $remaining = $this->count_expected - $this->count;
        if ($remaining < 0) {
            $remaining = 0;
        }

        return $remaining;
    }

    public function updateSlice()
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//
        //-----------------------------------[ SET UP ]---------------------------------//
        //-----------------------------------[ LOOP ]-----------------------------------//
        //---------------------------------[ SHUTDOWN ]---------------------------------//
        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        $the_table = $this->active_table;
        $sql = $this->slice_sql;
        if ($sql) {
            $sql = ' WHERE '.$sql;
        }
        $pie_table = self::find($this->slice_of_id)->active_table;
        DB::statement('TRUNCATE TABLE '.$the_table);
        DB::statement('INSERT '.$the_table.' SELECT * FROM '.$pie_table.$sql);
        $this->count = DB::table($the_table)->count();
        $this->enriched = 1;
        $this->save();
        $this->touch(); //update updated_at even if no changes made
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	COPY
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function copy($new_name = null)
    {

        //---------------------------------[ ARGUMENTS ]--------------------------------//
        //-----------------------------------[ SET UP ]---------------------------------//
        //-----------------------------------[ LOOP ]-----------------------------------//
        //---------------------------------[ SHUTDOWN ]---------------------------------//
        //-----------------------------[ RETURN TO WORKER ]-----------------------------//

        $copy = new self($this->type, $this->team_id);
        $copy->setGroupSlug();

        $new_table = $copy->table_bench;

        DB::statement('CREATE TABLE '.$new_table.' LIKE '.$this->active_table);
        DB::statement('INSERT '.$new_table.' SELECT * FROM '.$this->active_table);

        $copy->parent_id = $this->id;
        $copy->count = $this->count;
        $copy->notes = $this->notes;
        $copy->slice_of_id = $this->slice_of_id;
        $copy->slice_sql = $this->slice_sql;
        $copy->enriched = $this->enriched;
        if (! $new_name) {
            $new_name = 'Copy of '.$this->name;
        }
        $copy->name = $new_name;
        $copy->save();

        return 0;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	ENRICH
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function enrich()
    {
        //-------------------------------[ ARGUMENTS ]-------------------------------//

        $table = $this->active_table;

        session(['team_table' => $table]);

        //---------------------------------[ SET UP ]--------------------------------//

        // alter table x_0001_0001 ADD PRIMARY KEY (id)

        if (! $this->count_pointer) {
            $this->count_pointer = 0;
            $this->count_expected = DB::table($this->active_table)->count();
            $this->save();

            $this->dropIndexes($exceptions = ['PRIMARY']);
        }

        //---------------------------------[ LOOP ]---------------------------------//

        $voters = Voter::skip($this->count_pointer)->take(100)->get();

        $count = $this->count_pointer;

        /*

        $sql = 'INSERT '.$this->active_table.' (id, full_name, full_name_middle, household_id, full_address, address_zip, address_zip4) VALUES ';

        $middle_sql = '';

        foreach($voters as $thevoter) {

            $middle_sql .= '("'.$thevoter->id.'", "'.
                    $thevoter->first_name.' '.$thevoter->last_name.'", "'.
                    $thevoter->first_name.' '.$thevoter->middle_name.' '.$thevoter->last_name.'", "'.
                    $thevoter->generateHouseholdId().'", "'.
                    $thevoter->generateFullAddress().'",'.
                    '"'.substr($thevoter->address_zip,0,5).'",'.
                    '"'.substr($thevoter->address_zip,5).'"'.
                '),';
            $count++;
        }

        $middle_sql = rtrim($middle_sql,',');
        $sql = $sql.$middle_sql.' ON DUPLICATE KEY UPDATE full_name = VALUES(full_name), full_name_middle = VALUES(full_name_middle), household_id = VALUES(household_id),full_address = VALUES(full_address), address_zip = VALUES(address_zip),address_zip4 = VALUES(address_zip4)';

        */

        try {

            //DB::statement($sql);

            foreach ($voters as $voter) {
                $voter->save();
                $count++;
            }
        } catch (\Exception $e) {
            return ['error' 	=> true,
                    'function' 	=> 'enrich()',
                    'part'		=> 'SQL insert',
                    'name'		=> $this->name,
                    'at' 		=> $this->count_pointer, ];
        }

        $this->count_pointer = $count;
        $this->save();
        $remaining = $this->count_expected - $this->count_pointer;
        if ($remaining < 0) {
            $remaining = 0;
        }

        //---------------------------------[ SHUTDOWN ]---------------------------------//
        if ($remaining == 0) {
            $this->restoreIndexes($exceptions = ['PRIMARY']);
        }

        //-----------------------------[ RETURN TO WORKER ]-----------------------------//
        return $remaining;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	MERGE
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function merge($arguments)
    {

        //-------------------------------[ ARGUMENTS ]-------------------------------//

        $merge_this_id = $arguments['merge_this_id'];
        $merge_into_id = $arguments['merge_into_id'];

        $merge_this = self::find($merge_this_id);

        //---------------------------------[ SET UP]---------------------------------//

        if (! Schema::hasColumn($this->active_table, 'job_merged')) {
            Schema::table($this->active_table, function (Blueprint $table) {
                $table->boolean('job_merged')->default(0);
            });

            $newvoters = DB::table($merge_this->active_table)->get();

            $this->count_pointer = 0;
            $this->count_expected = $newvoters->count();
            $this->save();

            if (! Schema::hasColumn($this->active_table, 'merge_report')) {
                Schema::table($this->active_table, function (Blueprint $table) {
                    $table->longText('merge_report')->nullable();
                });
            }
        }

        //---------------------------------[ LOOP ]---------------------------------//

        $newvoters = DB::table($merge_this->active_table)
                        ->skip($this->count_pointer)
                        ->take(100)
                        ->get();
        $counter = 0;

        foreach ($newvoters as $thenew) {
            if (DB::table($this->active_table)->where('id', $thenew->id)->exists()) {
                $existing = DB::table($this->active_table)->where('id', $thenew->id)->first();
                $existing = collect($existing);
                $updating = collect($thenew);
                $diff = $updating->diff($existing);

                if ($diff->count() > 0) {

                    //THERE ARE DIFFERENCES TO MERGE
                    DB::table($this->active_table)->where('id', $thenew->id)->update($diff->toArray());

                    // RECORD CHANGES
                    $previous = $existing->diff($updating);
                    unset($previous['merge_report']); //Don't count as a difference
                    if (DB::table($this->active_table)->where('id', $thenew->id)->first()->merge_report) {
                        $merge_report = json_decode(DB::table($this->active_table)
                                                      ->where('id', $thenew->id)
                                                      ->first()
                                                      ->merge_report, true);
                        array_push($merge_report, $previous->toArray());
                        $changes = json_encode($merge_report);
                    } else {
                        $changes = json_encode([time() => $previous->toArray()]);
                    }

                    $changes = ['merge_report' => $changes];
                    DB::table($this->active_table)->where('id', $thenew->id)->update($changes);
                }
            } else {

                //ADDING A NEW RECORD
                $thenew->merge_report = null; //Don't insert this
                DB::table($this->active_table)->where('id', $thenew->id)->insert(collect($thenew)->toArray());

                // RECORD CHANGES
                if (DB::table($this->active_table)->where('id', $thenew->id)->first()->merge_report) {
                    $merge_report = json_decode(DB::table($this->active_table)->where('id', $thenew->id)->first()->merge_report, true);
                    array_push($merge_report, [time() => 'added']);
                    $changes = json_encode($merge_report);
                } else {
                    $changes = json_encode([time() => 'added']);
                }
                $changes = ['merge_report' => $changes];
                DB::table($this->active_table)->where('id', $thenew->id)->update($changes);
            }

            $counter++;
        }

        $this->count_pointer = $this->count_pointer + $counter;
        $this->save();
        $remaining = $this->count_expected - $this->count_pointer;
        if ($remaining < 0) {
            $remaining = 0;
        }

        //---------------------------------[ SHUTDOWN ]---------------------------------//

        if ($remaining == 0) {
            Schema::table($this->active_table, function (Blueprint $table) {
                $table->dropColumn('job_merged');
            });
        }

        //-----------------------------[ RETURN TO WORKER ]-----------------------------//
        return $remaining;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	HOUSEHOLDS
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function clearHouseholds()
    {
        $this->relatedHouseholds()->rollback($destroy_table = true, $destroy_import = true);

        return 0;
    }

    public function createHouseholds()
    {

        //$this->restoreIndexes();

        //-------------------------------[ ARGUMENTS ]-------------------------------//

        $hh_table_name = $this->table_bench.'_hh';
        session(['team_table' => $this->active_table]);
        session(['team_households_table' => $hh_table_name]);

        //---------------------------------[ SET UP]---------------------------------//

        if (! Schema::hasTable($hh_table_name)) {
            $hh_import = new self('hh', $this->team_id);
            $hh_import->version = $this->version;
            $hh_import->slug = $this->slug.'_hh';
            $hh_import->save();

            try {
                DB::statement('CREATE TABLE '.$hh_table_name.' LIKE x__template_households');

                DB::statement('INSERT '.$hh_table_name.' (id, household) SELECT DISTINCT household_id, full_address FROM '.$this->active_table);
            } catch (\Exception $e) {
                return ['error' 	=> true,
                        'function' 	=> 'createHouseholds()',
                        'part'		=> 'SQL create table and insert distinct HH ids',
                        'name'		=> $this->name,
                        'at' 		=> 0, ];
            }

            $count_expected = DB::table($hh_table_name)->count();

            $hh_import->count_expected = $count_expected;
            $hh_import->save();

            $remaining = $count_expected;

            return $remaining;
        }

        //---------------------------------[ LOOP ]---------------------------------//

        $current_row = $this->relatedHouseholds()->count_pointer;

        $voting_households = VotingHousehold::skip($current_row)->take(100)->get();

        $update_data = [];
        $update_keys = [];

        $sql = 'INSERT INTO '.$hh_table_name.' (id, residents, residents_count) VALUES ';
        $middle_sql = '';

        foreach ($voting_households as $thehousehold) {
            $residents = Voter::where('household_id', $thehousehold->id)->get();

            $residents_array = [];
            $residents_count = 0;

            foreach ($residents as $theresident) {
                $residents_array[] = $theresident->id;
                $residents_count++;
            }

            $middle_sql .= "('".$thehousehold->id."', '".json_encode($residents_array)."' ,'".$residents_count."'),";

            $current_row++;
        }

        if ($middle_sql) {
            //For some reason sometimes does not build the middle part
            $middle_sql = rtrim($middle_sql, ',');
            $sql = $sql.$middle_sql.' ON DUPLICATE KEY UPDATE residents = VALUES(residents), residents_count = VALUES(residents_count)';

            try {
                DB::statement($sql);
            } catch (\Exception $e) {
                return ['error' 	=> true,
                        'function' 	=> 'createHouseholds()',
                        'part'		=> 'SQL insert',
                        'name'		=> $this->name,
                        'at' 		=> $current_row, ];
            }
        }

        $hh_import = self::where('team_id', $this->team_id)
                               ->where('version', $this->version)
                               ->where('type', 'hh')
                               ->first();

        $hh_import->count_pointer = $current_row;
        $hh_import->save();

        $remaining = $hh_import->count_expected - $current_row;
        if ($remaining < 0) {
            $remaining = 0;
        }

        //---------------------------------[ SHUTDOWN ]---------------------------------//
        if ($remaining == 0) {
            // Done
        }
        //-----------------------------[ RETURN TO WORKER ]-----------------------------//
        return $remaining;
    }

    //////////////////////////////////////////////////////////////////////////////////
    //
    //	IMPORT
    //
    //////////////////////////////////////////////////////////////////////////////////

    public function import($arguments)
    {

        //
        // USING:
        // https://github.com/laravel/framework/pull/28614
        //

        //---------------------------------[ ARGUMENTS ]--------------------------------//
        $delimiter = $this->delimiter;
        $filename = $this->file_path;
        $skip_first = $this->skip_first;
        $header = json_decode($this->header_columns, true);
        $extra = json_decode($this->extra_columns, true);

        //-----------------------------------[ SET UP ]---------------------------------//
        if (! $this->count) {
            $this->setGroupSlug();
            DB::statement('CREATE TABLE '.$this->table_bench.' LIKE x__template_voters');

            $this->dropIndexes();

            $f = fopen($filename, 'rb');
            $total_records = 1;
            while (! feof($f)) {
                $total_records += substr_count(fread($f, 8192), "\n");
            }
            fclose($f);

            $this->count_expected = $total_records;
            $this->count = 0;
            $this->save();
        }

        //-----------------------------------[ LOOP ]-----------------------------------//
        $handle = fopen($filename, 'r');

        if ($delimiter == 'pipe') {
            $delimiter = '|';
        }
        if ($delimiter == 'comma') {
            $delimiter = ',';
        }

        fseek($handle, $this->file_pointer); //previous position in file

        $arr = [];
        $arr_extra = [];
        $extra_keys = [];

        foreach ($extra as $key => $value) {
            $arr_extra[$key] = $value;
            $extra_keys[] = $key;
        }

        for ($c = 0; $c < 100; $c++) {
            try {
                $csvLine = fgetcsv($handle, 5000, $delimiter);

                if ((empty($csvLine)) || (($this->count == 0) && ($skip_first))) {

                    //Skip if = First Row or if = Empty
                } else {
                    $arr_line = $arr_extra; //Start with extra override columns

                    foreach ($header as $key => $value) {
                        if (! in_array($value, $extra_keys)) { //Extra overrides header

                            if ($value != '{SKIP}') {
                                if (substr($value, 0, 10) == '{DATETIME}') {
                                    $value = trim(substr($value, 11, strlen($value)));
                                    $arr_line[$value] = Carbon::parse($csvLine[$key])->format('Y-m-d');
                                } else {
                                    $arr_line[$value] = $csvLine[$key];
                                }
                            }
                        }
                    }

                    $arr[] = $arr_line;
                }
            } catch (\Exception $e) {
                return ['error' 	=> true,
                        'function' 	=> 'import()',
                        'part'		=> 'csvLine',
                        'name'		=> $filename,
                        'at' 		=> $this->count, ];
            }

            $this->count++;
        }

        try {
            DB::table($this->active_table)->insert($arr);
        } catch (\Exception $e) {
            return ['error' 	=> true,
                    'function' 	=> 'import()',
                    'part'		=> 'SQL insert',
                    'name'		=> $filename,
                    'at' 		=> $this->count, ];
        }

        $this->file_pointer = ftell($handle); //new position
        $this->save();
        $remaining = $this->count_expected - $this->count;

        //---------------------------------[ SHUTDOWN ]---------------------------------//
        if ($remaining < 0) {
            $remaining = 0;
        }
        if ($remaining == 0) {
            $this->restoreIndexes();
        }

        //-----------------------------[ RETURN TO WORKER ]-----------------------------//
        return $remaining;
    }
}
