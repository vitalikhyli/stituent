<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use App\VoterMaster;
use App\Models\ImportedVoterMaster;

use DB;
use Carbon\Carbon;


class MergeMA extends NationalMaster
{
    protected $signature                = 'cf:ma_merge  {--table_master_merge_into}
                                                        {--clear_archived}
                                                        {--merge_only}
                                                        {--elections_only}
                                                        {--voter=}
                                                        {--set_archived_using_previous_table}';
    protected $description              = '';
    public $state                       = 'MA';

    ////////////////////////////////////////////////////////////////////////////////
    //
    // MERGE FUNCTION
    //

    public function mergeElections($new, $old)
    {
        //dd($new, $old);
        if (is_array($new) && !is_array($old)) {
            // need to both be arrays or just return the other
            return $new;
        }
        if (is_array($old) && !is_array($new)) {
            // need to both be arrays or just return the other
            return $old;
        }
        $elections = [];
        if (is_array($old) && is_array($new)) {
            foreach ($old as $election_id => $details) {
                $elections[$election_id] = $details;
            }
            foreach ($new as $election_id => $details) {
                if (isset($old[$election_id])) {
                    continue;
                }
                // either new election OR has no town
                if (strpos($election_id, 'need') > 0) {
                    $ignore = false;
                    foreach ($old as $old_election_id => $old_details) {
                        if (substr($election_id, 0, 19) == substr($old_election_id, 0, 19)) {
                            $ignore = true;
                            break;
                        }
                    }
                    if (!$ignore) {
                        $elections[$election_id] = $details;
                    } 
                } else {
                    $elections[$election_id] = $details;
                }
            }
            ksort($elections);
        }
        return $elections;
    }

    public function mergeRow($new, $old)
    {
        if ($this->options('elections_only')) {
            if ($old->elections) {

                if ($new->elections == $old->elections) return;

                $elections = $this->mergeElections($new->elections, $old->elections);
                $new->elections = $elections;
                $new->save();
            }
            return;
        }

        //--------------------------------------------------------------------------------

        $before = collect($new->getAttributes());

        //--------------------------------------------------------------------------------
        // ADMIN

        // 0 => "import_order"
        // 1 => "id"
        // 35 => "state"
        // 53 => "archived_at"
        // 56 => "created_by"
        // 57 => "updated_by"
        // 58 => "deleted_at"
        // 60 => "updated_at"
        if ($old->created_at) {
            $new->created_at = $old->created_at;
        }

        //--------------------------------------------------------------------------------
        // ORIGINAL DATA

        // 54 => "origin_method"
        // 55 => "original_import"

        $new->original_import = collect($new->original_import)
                                ->union(collect($old->original_import))
                                ->toArray();
        

        //--------------------------------------------------------------------------------
        // CALCULATED

        // 2 => "full_name"
        // 3 => "full_name_middle"
        // 4 => "household_id"
        // 6 => "full_address"

        //--------------------------------------------------------------------------------
        // NOT USED
        // 33 => "ethnicity"
        // 34 => "head_household"
        // 44 => "spouse_name"
        // 52 => "alternate_districts"

        //--------------------------------------------------------------------------------
        // ELECTIONS

        // 7 => "elections"

        if ($old->elections) {
            $elections = $this->mergeElections($new->elections, $old->elections);
            //dd($old->elections, $new->elections, $elections);
            $new->elections = $elections;
        }
        //--------------------------------------------------------------------------------
        // NAME

        // 8 => "name_title"
        // 9 => "first_name"
        // 10 => "middle_name"
        // 11 => "last_name"
        // 12 => "suffix_name"

        foreach(['name_title', 
                'first_name', 
                'middle_name', 
                'last_name', 
                'suffix_name'] as $field) {

            if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
        }

        

        if ($this->addressesMatch($old, $new)) {

            //--------------------------------------------------------------------------------
            // ADDRESS

            // 13 => "address_prefix"
            // 14 => "address_number"
            // 15 => "address_fraction"
            // 16 => "address_street"
            // 17 => "address_street_type"
            // 18 => "address_post"
            // 19 => "address_apt_type"
            // 20 => "address_apt"
            // 21 => "address_city"
            // 22 => "address_state"
            // 23 => "address_zip"
            // 24 => "address_zip4"
            // 25 => "address_lat"
            // 26 => "address_long"
        
            foreach([
                // 'address_prefix',
                //      'address_number',
                //      'address_fraction',
                //      'address_street',
                //      'address_street_type',
                //      'address_post',
                //      'address_apt_type',
                //      'address_apt',
                //      'address_city',
                //      'address_state',
                //      'address_zip',
                //      'address_zip4',
                     'address_lat',
                     'address_long',
                     'mass_gis_id',
                     'mailing_info'] as $field) {
                
                if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
            }

            //--------------------------------------------------------------------------------
            // DISTRICTS

            // 36 => "governor_district"
            // 37 => "congress_district"
            // 38 => "senate_district"
            // 39 => "house_district"
            // 40 => "county_code"
            // 41 => "city_code"
            // 42 => "ward"
            // 43 => "precinct"

            foreach(['governor_district',
                     // 'congress_district',
                     // 'senate_district',
                     // 'house_district',
                     // 'county_code',
                     // 'city_code',
                     // 'ward',
                     // 'precinct'
                     ] as $field) {
                
                if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
            }
        }

        

        //--------------------------------------------------------------------------------
        // ELECTIONS

        // 27 => "gender"
        // 28 => "party"
        // 29 => "dob"
        // 30 => "yob"
        // 31 => "registration_date"
        // 32 => "voter_status"

        foreach(['gender',
                 'party',
                 'dob',
                 'yob',
                 'registration_date',
                 'voter_status'
                 ] as $field) {
            
            if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
        }

        

        //--------------------------------------------------------------------------------
        // CONTACT INFO

        // 45 => "cell_phone"
        // 46 => "home_phone"
        // 49 => "mailing_info"
        // 50 => "emails"

        foreach(['cell_phone',
                 'home_phone',
                 ] as $field) {
            
            if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
        }

        $emails = (!$new->emails) ? null : $new->emails; // Make sure it's not null
        if (!$emails) $emails = ($old->emails) ?: $emails;

        //--------------------------------------------------------------------------------
        // MISC

        // 47 => "deceased"
        // 48 => "deceased_date"
        // 51 => "business_info"

        foreach(['deceased',
                 'deceased_date',
                 'business_info'
                 ] as $field) {
            
            if (!$new->$field) $new->$field = ($old->$field) ?: $new->$field;
        }

        //--------------------------------------------------------------------------------
        // SAVE

        $new->save();

        // $diff = collect($new->getAttributes())->diffAssoc($before);

        // if ($diff->first()) {
        //     // echo "\n".$new->id." | ".$new->full_name."\n";
        //     // print_r($diff->except(['updated_at'])->toArray());
        // }
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // OTHER FUNCTIONS
    //

    public function __construct()
    {
        parent::__construct();
    }

    public function partOne($old, $new)
    {     
        echo $this->basicLine();
        $this->info('PART I: NEW rows not found in OLD: No action needed.');
        echo $this->venn('new');
    }

    public function partTwo($old, $new)
    {
        echo $this->basicLine();
        $this->info('PART II: OLD rows not found in NEW...');
        echo $this->venn('old');

        $this->expected_num_rows    = null;

        $archive_table = 'to_archive_'.time();
        
        $sqls = [];

        $sqls[] = ['statement'   => "CREATE TABLE $archive_table
                                      AS SELECT id FROM $old",
                   'message'     => "Creating table: $archive_table",
                   'max_retry'   =>  1];

        $sqls[] = ['statement'   => "ALTER TABLE $archive_table ADD PRIMARY KEY (id)",
                   'message'     => "Adding primary key to ARCHIVE",
                   'max_retry'   =>  1];

        $sqls[] = ['statement'   => "DELETE $archive_table FROM $archive_table 
                                     JOIN $new
                                     ON $archive_table.id = $new.id",
                   'message'     => "Removing rows from ARCHIVE that also appear in NEW",
                   'max_retry'   =>  2];

        $sqls[] = ['statement'   => "INSERT INTO $new
                                     (SELECT $old.* FROM $old 
                                     JOIN $archive_table
                                     ON $old.id = $archive_table.id)",
                   'message'     => "Copying OLD rows to NEW if those voters are in ARCHIVE",
                   'max_retry'   =>  2];

        $sqls[] = ['statement'   => "ALTER TABLE $new ADD INDEX (archived_at)",
                   'message'     => "Adding index for archived_at to NEW",
                   'max_retry'   =>  1];


        $sqls[] = ['statement'   => "ALTER TABLE $archive_table 
                                     ADD COLUMN updated_new BOOL DEFAULT 0",
                   'message'     => "Adding updated_new boolean to ARCHIVE to keep track",
                   'max_retry'   =>  1];

        $sqls[] = ['statement'   => "ALTER TABLE $archive_table ADD INDEX (updated_new)",
                   'message'     => "Adding index for updated_new to ARCHIVE",
                   'max_retry'   =>  1];

        $s = 0;

        while($s < count($sqls)) {

            if (!isset($sqls[$s]['retries'])) {
                $sqls[$s]['retries'] = 0;
            }

            $sql  = $sqls[$s];


            $this->start_time           = Carbon::now();
            $this->info('Running SQL '.($s + 1).' of '.count($sqls)."\n".$sql['message']);

            try {

                DB::connection('voters')->statement($sql['statement']);
                $s++;
                $this->info('Done.');


            } catch (\Exception  $e) {

                if ($sql['retries'] >= $sql['max_retry']) {

                    $this->error($e->getMessage());
                    dd('Error -- statement not executed. Max re-tries exhausted.');

                } else {

                    $sqls[$s]['retries']++;
                    $this->error('Error -- Trying statement again. Re-try attempt '
                                 .$sqls[$s]['retries']
                                 .' of '.$sqls[$s]['max_retry']);

                }

            }

            echo $this->progress($row = null)."\r";
            echo "\n\n";
        }


        ////////////////////////////////////////////////////////////////////////////
        
        $this->start_time = Carbon::now();
        $this->info('Running Special SQL Loop to update archived_at for certain rows in NEW');

        $archive_time   = Carbon::now();
        $chunk          = 550;
        $i              = 1;
        $total_archived = 0;
        $go             = true;

        while ($go != false) {

            try {

                $ids = DB::connection('voters')
                         ->table($archive_table)
                         ->where('updated_new', false)
                         ->limit($chunk)
                         ->pluck('id');

                if (count($ids) == 0) break;

                $num_affected = DB::connection('voters')->table($new)
                  ->whereNull('archived_at')
                  ->whereIn('id', $ids)
                  ->update(['archived_at' => $archive_time]);

                DB::connection('voters')->table($archive_table)
                  ->whereIn('id', $ids)
                  ->update(['updated_new' => true]);

                $total_archived += $num_affected;

                echo 'Loop '.$i.' / # Archived rows: '.number_format($total_archived)."\r";
                $i++;

            } catch (\Exception  $e) {

                $this->error($e->getMessage());
                dd('Error with Update archived_at loop.');

            }

        }
        echo "\n\n";

        ////////////////////////////////////////////////////////////////////////////

        $archive_table_count = DB::connection('voters')->table($archive_table)->count();
        $this->info(number_format($archive_table_count).' rows not found in '.$new);
        echo "\n";
    }

    public function partThree($old, $new)
    {
        echo $this->basicLine();
        $this->info('PART III: Merging overlapping voters...');
        echo $this->venn('overlap');

        session(['table_while_importing_master' => $new]);  // Sets ImportedVoterMaster
        session(['team_state' => $this->state]);            // Sets VoterMaster

        echo 'Counting...'."\n";
        $this->expected_num_rows    = 5017552; //ImportedVoterMaster::count(); //4810982;
        $this->start_time           = Carbon::now();
        $log                        = $this->createErrorLog('merge');
        $error_count                = 0;
        $row                        = 1;
        $chunk                      = 250;

        $this->info('Going through '.$new.' in chunks of '.$chunk);

        if ($this->option('voter')) {
            echo $this->option('voter')."\n";
            $voter = ImportedVoterMaster::find($this->option('voter'));
            if (!$old = VoterMaster::find($voter->id)) {
                // No need to merge anything
                return;
            }
            echo "Merging\n";
            $this->mergeRow($new = $voter, $old);
            echo "Done.\n";
            return;
        }

        ImportedVoterMaster::chunkById($chunk, function($voters) use (&$row, &$error_count, &$log) {

            foreach ($voters as $voter) {

                echo $this->progress($row++)."\r";
                
                try {
                    $new = $voter;
                    if (!$old = VoterMaster::find($voter->id)) {
                        // No need to merge anything
                        if (!$new->full_address) {
                            $new->save();
                        }
                        continue;
                    }

                    $this->mergeRow($new, $old);

                } catch (\Exception $e) {

                    $log->error($e->getMessage());
                    $error_count++;
                    $this->error('Error id: '.$voter->id.' -- see log.');

                }

            }

        });
    }

    public function handle()
    {
        // if (config('app.env') != 'local') dd('Cannot run in live yet.');

        $old = 'x_voters_'.$this->state.'_master';

        //////////////////////////////////////////////////////////////////////////////

        $table_master_merge_into = ($this->option('table_master_merge_into')) ?: null;

        if ($table_master_merge_into) {

            if ($table_master_merge_into = '{MOST RECENT MASTER}') {
                $new = $this->getMostRecentMaster($this->state);
            } else {
                $new = $this->option('table_master_merge_into');
            }

        } else {

            $new = $this->selectPreviousMaster($this->state);
        }   

        $this->info('This command will try to merge the current '.$old.' table into '.$new);


        //////////////////////////////////////////////////////////////////////////////

        if ($this->option('clear_archived')) {

            $this->info('Clearing records from new master where archived_at is not null');
            
            $statement = "DELETE FROM $new WHERE archived_at IS NOT NULL";

            try {

                DB::connection('voters')->statement($statement);

                $this->info('Archived_at rows cleared.');

            } catch (\Exception $e) {

                $this->error($e->getMessage());
                dd();

            }

        }

        if ($this->option('set_archived_using_previous_table')) {

            $previous_archive_table = $this->selectPreviousToArchiveTable();

            echo $this->redLineMessage('**** SETTING ARCHIVED_AT FROM OLD ARCHIVE TABLE ****');
            $ok = $this->confirm("Are you sure you want to set archived_at from $new\n...where joined on $previous_archive_table?", true);
            if (!$ok) dd('Exiting...');

            $sql = ['statement'   => "UPDATE $new 
                                         JOIN $previous_archive_table
                                         ON $new.id = $previous_archive_table.id
                                         SET archived_at = '".date('Y-m-d H:i:s')."'
                                         WHERE archived_at IS NULL",
                       'message'     => "Setting archived_at from: $previous_archive_table",
                       'max_retry'   =>  1];
            $this->start_time           = Carbon::now();

            //dd($sql);
            $this->info('Running SQL '.$sql['message']);
            DB::connection('voters')->statement($sql['statement']);
            dd();
        }

        //////////////////////////////////////////////////////////////////////////////

        if (!$this->option('merge_only')) $this->partOne($old, $new);

        if (!$this->option('merge_only')) $this->partTwo($old, $new);

        $this->partThree($old, $new);

        //////////////////////////////////////////////////////////////////////////////
        
        echo "\n";
        echo "Merge done!\n";
        $this->info("$old rows archived where needed\n$old merged into $new");

    }

    public function venn($name = null)
    {
        $art = "
        +------------------+                 
        | OLDE 111111111111|                               
        |11111111+------------------+
        |11111111|333333333|22222222|
        |11111111|333333333|22222222|
        +--------|---------+22222222|
                 |2222222222222 NEW |
                 +------------------+
        \n"; //textik.com

        $arr = [1, 2, 3];
        $highlighted = array_search($name, ['old', 'new', 'overlap']) + 1;
        if ($highlighted) unset($arr[$highlighted - 1]);

        if ($highlighted == 1) $art = str_replace(1, $this->r1." ".$this->color_reset, $art);
        if ($highlighted == 2) $art = str_replace(2, $this->c1." ".$this->color_reset, $art);
        if ($highlighted == 3) $art = str_replace(3, $this->p1." ".$this->color_reset, $art);
    
        foreach($arr as $n) {
            $art = str_replace($n, " ", $art);
        }

        return $art;
    }

    public function addressesMatch($old, $new)
    {
        $matches_needed = ['address_number', 'address_street', 'address_city'];
        foreach ($matches_needed as $field) {
            if (strtoupper($old->$field) != strtoupper($new->$field)) {
                return false;
            }
        } 
        return true;
    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
}
