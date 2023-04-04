<?php

namespace App\Console\Commands\Admin\States;

// About Extending Command Classes:
// "By making the parent class abstract you don't need to define the signature"
// https://stackoverflow.com/questions/55738140/unable-to-deeply-extend-laravel-artisan-command-with-input-passed-down

use Illuminate\Console\Command;

use Carbon\Carbon;
use Illuminate\Support\Str;

use DB;
use Schema;
use Illuminate\Database\Schema\Blueprint;

use App\District;
use App\County;
use App\Municipality;
use App\VoterSlice;

use App\Traits\Admin\FileProcessingTrait;


abstract class NationalMaster extends Command
{
    use FileProcessingTrait;

    abstract function forEachRow($switch, $row, $row_num);

    public $national_template           = 'x_voters_MA_master';
    public $storage_subdir              = '/uploads-master-voter';
    public $elections_storage_subdir    = '/uploads-election-history';

    public $table_name;
    public $firstrow;
    public $delimiter;
    public $expected_num_rows;
    public $start_time;

    public $origin_method               = 'STATEWIDE_EMERGES';
    public $original_import_date        = '2020-03-01';

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // MISC
    //

    public function stateSubCommand($signature)
    {
        return 'cf:'.strtolower($this->state).'_'.$signature;
    }

    public function commandMustExistOrDie($command)
    {
        if (!in_array($command, collect(\Artisan::all())->keys()->toArray())) {
            dd($command.' command does not exist. Exiting.');
        }
    }

    public function englishOrdinal($string)
    {
        $num = preg_replace('/[^0-9]/', '', $string);

        if (!$num) return null;

        $ordinals_array = ['First', 'Second', 'Third', 'Fourth', 'Fifth', 'Sixth', 'Seventh', 'Eighth', 'Ninth', 'Tenth', 'Eleventh', 'Twelfth', 'Thirteenth', 'Fourteenth', 'Fifteenth', 'Sixteenth', 'Seventeenth', 'Eighteenth', 'Nineteenth', 'Twentieth', 'Twenty-first', 'Twenty-second', 'Twenty-third', 'Twenty-fourth', 'Twenty-fifth', 'Twenty-sixth', 'Twenty-seventh', 'Twenty-eighth', 'Twenty-ninth', 'Thirtieth', 'Thirty-first', 'Thirty-second', 'Thirty-third', 'Thirty-fourth', 'Thirty-fifth', 'Thirty-sixth', 'Thirty-seventh'];
         
        return trim($ordinals_array[$num - 1]);
    }

    public function alphaOnly($string)
    {
        $string = str_replace('-', ' ', $string);
        return trim(preg_replace('/[^a-z\s-]/i', '', $string));
    }

    public function formatParty($party)
    {
        $party = substr($party, 0, 1);
        if ($party == 'N') {
            return 'U';
        }
        return $party;
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // CALL OTHER COMMANDS
    //

    public function populateStateSlices($overwrite_option)
    {
        $slices = VoterSlice::where('name', 'like', 'x_'.$this->state.'%')->get();

        foreach($slices as $slice) {

            $this->info('Populating '.$slice->name);
            
            if ($overwrite_option) {

                $this->call('cf:populate_slices', [
                    '--slice' => $slice->name, 
                    '--overwrite' => 'default'
                ]);

            } else {

                $this->call('cf:populate_slices', [
                    '--slice' => $slice->name
                ]);

            }

        }

    }


    
    ////////////////////////////////////////////////////////////////////////////////////
    //
    // MANIPULATE MASTER TABLES
    //

    public function addCommentToTable($table, $new_comment)
    {
        $previous_comments = null;

        $previous_comments_sql = "SELECT table_comment
                                  FROM INFORMATION_SCHEMA.TABLES
                                  WHERE table_name='$table'";

        $previous_comments = collect(
                                DB::connection('voters')->select($previous_comments_sql)
                             )->first()
                              ->table_comment;

        if ($previous_comments) $previous_comments = "\n".$previous_comments;
        
        $comment = $new_comment.$previous_comments;

        $comment_sql = 'ALTER TABLE '.$table.' COMMENT "'.$comment.'"';

        DB::connection('voters')->statement($comment_sql);
    }


    public function activateMaster($the_table_name)
    {
        try {

            $master_table = 'x_voters_'.$this->state.'_master';

            if (Schema::connection('voters')->hasTable($master_table)) {
                $archived_master = $master_table.'_archived_'.time();

                $archive_sql = 'RENAME TABLE '.$master_table.' TO '.$archived_master.';';

                DB::connection('voters')->statement($archive_sql);

                $comment = Carbon::now()->format('F n, Y @ h:i A').
                           ' - [voters] '.$master_table
                           .' --> [voters] '
                           .$archived_master;

                $this->addCommentToTable($archived_master, $comment);

                $this->info($master_table."\t\t---->\t".$archived_master);
                
            }

            $new_sql = 'RENAME TABLE '.$the_table_name.' TO '.$master_table.';';

            DB::connection('voters')->statement($new_sql);

            $comment = Carbon::now()->format('F n, Y @ h:i A').
                       ' - [voters] '.$the_table_name
                       .' --> [voters] '
                       .$master_table;

            $this->addCommentToTable($master_table, $comment);

            $this->info($the_table_name."\t---->\t".$master_table);

            return true;

        } catch (\Exception $e)  {

            dd('There was an error renaming tables.', $e->getMessage());

        }
    }

    public function getListOfPreviousToArchiveTables()
    {
        $list = [];
        foreach (DB::connection('voters')->select('SHOW TABLES') as $table) {
            foreach ($table as $key => $name) {
                if (substr($name,0,11) == 'to_archive_') {
                    $timestamp = substr($name, -10);
                    $list[] = $name."\t"
                              ." rows: ".str_pad(
                                  number_format(
                                      DB::connection('voters')->table($name)->count()
                                    )
                                , 10, " ")." "
                              .$this->tableTimestampReadable($timestamp);
                }
            }
        }

        $list = collect($list)->sortDesc()->take(5);
        $list = $this->rekeyStartingAtOne($list->toArray());
        $list = collect($list); 
        
        return $list;
    }

    public function selectPreviousToArchiveTable()
    {
        $list = $this->getListOfPreviousToArchiveTables();

        if (!$list->first()) {

            $this->error('There are no archives to use.');
            dd();

        } else {

            $previous = $this->choice('Choose a previous '.$this->state.' to_archive_ table', $list->toArray());

            $previous = trim(substr($previous, 0, strpos($previous, "\t")));

            return $previous;
        }
        
    }

    public function getListOfPreviousMasters($archived = null)
    {
        $list = [];
        foreach (DB::connection('voters')->select('SHOW TABLES') as $table) {
            foreach ($table as $key => $name) {

              if (!$archived) {

                if (
                    (substr($name,0,19) == 'x_voters_'.$this->state.'_master_') &&
                    (!strpos($name, '_archived'))
                    ) {
                    $timestamp = substr($name, -10);
                    $list[] = $name."\t\t".$this->tableTimestampReadable($timestamp);
                }

              }

              if ($archived) {

                if (
                    (substr($name,0,19) == 'x_voters_'.$this->state.'_master_') &&
                    (strpos($name, '_archived'))
                    ) {
                    $timestamp = substr($name, -10);
                    $list[] = $name."\t".$this->tableTimestampReadable($timestamp, $color = 'red');
                }

              }

            }
        }

        $list = collect($list)->sortDesc()->take(10);
        $list = $this->rekeyStartingAtOne($list->toArray());
        $list = collect($list); 
        
        return $list;
    }

    public function selectPreviousMaster($include_archived = null)
    {
        $list = $this->getListOfPreviousMasters();

        if ($include_archived) {
          // $list->prepend($this->getMaster($this->state)
          //                ."\t\t"
          //                .$this->r1.'<-- CURRENT MASTER TABLE'.$this->color_reset
          //               );

            $list = $list->merge($this->getListOfPreviousMasters($archived = true));

        }

        if (!$list->first()) {

            $this->error('There are no master tables to choose from.');
            dd();

        } else {

            $previous = $this->choice('Choose a table', $list->toArray());

            $previous = trim(substr($previous, 0, strpos($previous, "\t")));

            return $previous;
        }
        
    }

    public function getMaster($state)
    {
        $master = 'x_voters_'.$state.'_master';
        if (!Schema::connection('voters')->hasTable($master)) return null;
        return $master;
    }

    public function getMostRecentMaster($state)
    {
        $list = [];
        foreach (DB::connection('voters')->select('SHOW TABLES') as $table) {
            foreach ($table as $key => $name) {
                if (
                    (substr($name,0,19) == 'x_voters_'.$state.'_master_') &&
                    (!strpos($name, '_archived'))
                    ) {
                    $timestamp = substr($name, -10);
                    $list[$timestamp] = $name;
                }
            }
        }

        krsort($list);

        return collect($list)->first();
    }

    public function createNewMasterTable($state, $national_template)
    {
        ////////// Decide Name

        $new_table_name = 'x_voters_'.$state.'_master_'.time();

        if (Schema::connection('voters')->hasTable($new_table_name)) {
            dd('Error. Table exists already.');
        }

        ////////// Create New Based on Template

        /*

        CREATE TABLE `x_voters_MA_master` (
  `import_order` int(10) unsigned DEFAULT NULL,
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name_middle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `household_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mass_gis_id` int(10) unsigned DEFAULT NULL,
  `full_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `elections` text COLLATE utf8mb4_unicode_ci,
  `name_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suffix_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_prefix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_fraction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_street_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_post` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_apt_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_apt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_zip4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_lat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_long` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `party` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `yob` int(10) unsigned DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `voter_status` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ethnicity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `head_household` tinyint(1) DEFAULT NULL,
  `state` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `governor_district` int(10) unsigned DEFAULT NULL,
  `congress_district` int(10) unsigned DEFAULT NULL,
  `senate_district` int(10) unsigned DEFAULT NULL,
  `house_district` int(10) unsigned DEFAULT NULL,
  `county_code` int(10) unsigned DEFAULT NULL,
  `city_code` int(10) unsigned DEFAULT NULL,
  `ward` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precinct` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `spouse_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deceased` tinyint(1) NOT NULL DEFAULT '0',
  `deceased_date` date DEFAULT NULL,
  `mailing_info` text COLLATE utf8mb4_unicode_ci,
  `emails` text COLLATE utf8mb4_unicode_ci,
  `business_info` text COLLATE utf8mb4_unicode_ci,
  `alternate_districts` text COLLATE utf8mb4_unicode_ci,
  `archived_at` datetime DEFAULT NULL,
  `origin_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `original_import` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `x__template_voters_household_id_index` (`household_id`),
  KEY `x__template_voters_first_name_index` (`first_name`),
  KEY `x__template_voters_last_name_index` (`last_name`),
  KEY `x__template_voters_address_street_index` (`address_street`),
  KEY `x__template_voters_address_city_index` (`address_city`),
  KEY `x__template_voters_address_zip_index` (`address_zip`),
  KEY `x__template_voters_gender_index` (`gender`),
  KEY `x__template_voters_party_index` (`party`),
  KEY `x__template_voters_dob_index` (`dob`),
  KEY `x__template_voters_registration_date_index` (`registration_date`),
  KEY `x__template_voters_voter_status_index` (`voter_status`),
  KEY `x__template_voters_governor_district_index` (`governor_district`),
  KEY `x__template_voters_congress_district_index` (`congress_district`),
  KEY `x__template_voters_senate_district_index` (`senate_district`),
  KEY `x__template_voters_house_district_index` (`house_district`),
  KEY `x__template_voters_county_code_index` (`county_code`),
  KEY `x__template_voters_city_code_index` (`city_code`),
  KEY `x__template_voters_ward_index` (`ward`),
  KEY `x__template_voters_precinct_index` (`precinct`),
  KEY `x__template_voters_deceased_index` (`deceased`),
  KEY `x__id` (`id`) USING BTREE,
  KEY `x_import` (`import_order`) USING BTREE,
  KEY `idx_master_last_name_address_street` (`last_name`,`address_street`),
  KEY `idx_master_last_name_city_code` (`last_name`,`city_code`),
  KEY `idx_master_last_name_address_zip` (`last_name`,`address_zip`),
  KEY `idx_master_last_name_party` (`last_name`,`party`),
  KEY `idx_master_last_name_dob` (`last_name`,`dob`),
  KEY `idx_master_address_number_address_street` (`address_number`,`address_street`),
  KEY `idx_master_address_street_city_code` (`address_street`,`city_code`),
  KEY `idx_master_address_street_address_zip` (`address_street`,`address_zip`),
  KEY `idx_master_address_street_party` (`address_street`,`party`),
  KEY `idx_master_address_street_dob` (`address_street`,`dob`),
  KEY `idx_master_city_code_address_zip` (`city_code`,`address_zip`),
  KEY `idx_master_city_code_party` (`city_code`,`party`),
  KEY `idx_master_city_code_dob` (`city_code`,`dob`),
  KEY `idx_master_address_zip_party` (`address_zip`,`party`),
  KEY `idx_master_address_zip_dob` (`address_zip`,`dob`),
  KEY `idx_master_party_dob` (`party`,`dob`),
  KEY `idx_master_first_name_last_name` (`first_name`,`last_name`),
  KEY `idx_master_first_name_address_street` (`first_name`,`address_street`),
  KEY `idx_master_first_name_city_code` (`first_name`,`city_code`),
  KEY `idx_master_first_name_address_zip` (`first_name`,`address_zip`),
  KEY `idx_master_first_name_party` (`first_name`,`party`),
  KEY `idx_master_first_name_dob` (`first_name`,`dob`),
  KEY `idx_master_city_code_ward_precinct` (`city_code`,`ward`,`precinct`),
  KEY `idx_master_archived_at_updated_at` (`archived_at`,`updated_at`),
  KEY `idx_master_city_code_address_street` (`city_code`,`address_street`),
  KEY `x_voters_ma_master_mass_gis_id_index` (`mass_gis_id`),
  KEY `idx_master_address_city_last_name_first_name` (`address_city`,`last_name`,`first_name`),
  KEY `idx_master_archived_at_deceased` (`archived_at`,`deceased`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci

        */

        $create = DB::connection('voters')
                    ->select('SHOW CREATE TABLE '.$national_template);

        $create_sql = $create[0]->{'Create Table'};
        $create_sql = str_replace($national_template, $new_table_name, $create_sql);
        $create_sql = preg_replace('/\s+KEY `.*\n/', '', $create_sql);
        $create_sql = str_replace('),)', '))', $create_sql);
        

        DB::connection('voters')->statement($create_sql);

        //////// Non-Standard Adjustments

        if ($state == 'RI') {

            $lengthen_voter_status_sql = 'ALTER TABLE '.$new_table_name
                                        .' MODIFY COLUMN voter_status VARCHAR(2)';

            DB::connection('voters')->statement($lengthen_voter_status_sql);

        }

        //////// Ensure Latest Rows

        if (!Schema::connection('voters')->hasColumn($new_table_name, 'original_import')) {
            Schema::connection('voters')->table($new_table_name, function (Blueprint $table)
            {
                $table->text('original_import')->nullable()->after('origin_method');
            });
        }

        if (!Schema::connection('voters')->hasColumn($new_table_name, 'yob')) {
            Schema::connection('voters')->table($new_table_name, function (Blueprint $table)
            {
                $table->unsignedInteger('yob')->nullable()->after('dob');
            });
        }

        ////////// Return

        return $new_table_name;
    }


    ///////////////////////////////////////////////////////////////////////////////////
    //
    // CORE FUNCTIONS
    //

    public function importNewMaster($file_path)
    {
        $this->info('Importing voter file...');

        ////////////////////////////////////////////////////////////////////

        try {

            $this->table_name = $this->createNewMasterTable($this->state, 
                                                            $this->national_template);

            $this->info($this->table_name.' was created, '.$this->condescendingNickname().'.');

        } catch(\Exception $e) {
            
            $this->error($e);
            dd('Error - new table not created, '.$this->condescendingNickname().'.');

        }
        
        session(['table_while_importing_master' => $this->table_name]);

        ////////////////////////////////////////////////////////////////////

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter    = $this->detectDelimiter($file_path);
        $this->firstrow     = $this->getFirstRow($file_path);
        $this->start_time   = Carbon::now();

        $log = $this->createErrorLog($this->table_name);

        $result = $this->massInsert($file_path,
                                    $state_function = 'voters',
                                    $log,
                                    500);
        
        ////////////////////////////////////////////////////////////////////

        $total_rows = $result['last_row_num'] - 1;
        $finished   = Carbon::now();

        if ($result['error_count'] > 0) {
            $error_msg = $this->r1.'Errors: '.number_format($result['error_count']).' ----> See log: "'.$log->getName().'"'.$this->color_reset;
        }

        echo "\n";
        $this->info("Processed: ".number_format($total_rows)." voters \n");
        if (isset($error_msg)) echo $error_msg."\n";
    }


    // public function chooseAndRunCommands()
    // {
    //     ///////////////////////////////////////////////////////////////////////

    //     $this->showCommunityFluencyWordMark();

    //     $this->blueLineMessage('*** This is the Master Command For '.$this->state
    //                            .' ('.$this->fullStateName($this->state).') ***');

    //     ///////////////////////////////////////////////////////////////////////

    //     $commands_to_run = $this->basketOfItems($prompt = 'Add a command to the run list',
    //                                             $choices = ['Import Voters',
    //                                                         'Import Elections',
    //                                                         'Populate '.$this->state.' Slices',
    //                                                         'Count Districts']);

    //     if (!$commands_to_run) {
    //         $this->error('No commands chosen');
    //         dd();
    //     }

    //     $summary = ["OK, we'll be running these commands:"];
    //     $n = 1;
    //     foreach($commands_to_run as $command) {
    //         $summary[] = $n++.' - '.str_replace("\n", '', $command);
    //     }
    //     $this->bannerMessage($summary);

    //     $run_import     = (in_array('Import Voters', $commands_to_run));
    //     $run_elections  = (in_array('Import Elections', $commands_to_run));
    //     $run_populate   = (in_array('Populate '.$this->state.' Slices', $commands_to_run));
    //     $run_count      = (in_array('Count Districts', $commands_to_run));
        
    //     ///////////////////////////////////////////////////////////////////////

    //     if ($run_import || $run_elections || $run_populate || $run_count) {

    //         $master_choices = [];

    //         if ($run_import) {
    //             $master_choices['imported'] = 'The New Voter Import';
    //         }
    //         if ($this->getListOfPreviousMasters()->first()) {
    //             $master_choices['previous'] = 'A Previous Voter Import';
    //         }
    //         if ($this->getMaster($this->state)) {
    //             $master_choices['existing'] = 'Keep Existing Master';
    //         }

    //         $this->rekeyStartingAtOne($master_choices);

    //         $which_master = $this->choice("\n".'Set as Master Voter File', $master_choices);

    //         if ($which_master == 'previous') {
    //             $selected_previous_master = $this->selectPreviousMaster(); // Choose it now
    //         }

    //     }

    //     ///////////////////////////////////////////////////////////////////////

    //     if ($run_import) {
    //         $this->checkDistricts();
    //         $this->checkMunicipalities();
    //         $file_path = $this->selectFilePath($this->storage_subdir, "the VOTER import");
    //     }
        
    //     if ($run_elections) {
    //         $this->commandMustExistOrDie($this->stateSubCommand('elections'));
    //         $election_file_path = $this->selectFilePath($this->elections_storage_subdir, "the ELECTIONS import");
    //     }
        
    //     if ($run_populate) {
    //         $overwrite_option = $this->confirm('Populate Slices: Overwrite / update previous slices?', true);
    //     }

    //     //////////////////////////////////////////////////////////////////////////////

    //     if ($run_import)    $this->importNewMaster($file_path);

    //     if (isset($which_master)) {

    //         switch($which_master) {
    //             case 'imported':
    //                 $this->activateMaster($this->table_name); //Set by importNewMaster();
    //                 break;

    //             case 'previous':
    //                 $this->activateMaster($selected_previous_master); // Set at beginning
    //                 break;

    //             case 'existing':
    //                 // No action needed, because using existing master table
    //                 break;
    //         }

    //     }

    //     //////////////////////////////////////////////////////////////////////////////

    //     if ($run_elections) $this->call($this->stateSubCommand('elections'), 
    //                                     ['--file_path' => $election_file_path]);
    //     if ($run_populate)  $this->populateStateSlices($overwrite_option);
    //     if ($run_count)     $this->call('cf:count_slices', ['--state' => $this->state]);

    //     //////////////////////////////////////////////////////////////////////////////
        
    //     $this->echoDone();

    // }

}
