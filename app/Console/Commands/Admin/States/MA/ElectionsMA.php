<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use App\Municipality;
use App\Models\ImportedVoterMaster;   
use App\Models\ImportedMAElection;
use App\Models\ImportedMAElectionVoter;
use App\Models\ImportedMAElectionCity;

use Carbon\Carbon;

use DB;
use Schema;
use Illuminate\Database\Schema\Blueprint;


class ElectionsMA extends NationalMaster
{
    protected $signature                = 'cf:ma_elections {--clear_elections}
                                                           {--clear_election_voter}
                                                           
                                                           {--file_path_elections=}
                                                           {--file_path_election_voter=}

                                                           {--table_lookup_cities=}
                                                           {--table_add_elections=}';
    protected $description              = '';
    public $state                       = 'MA';

    public $elections_table             = 'i_ma_elections_import';
    public $pivot_table                 = 'i_ma_election_voter_import';
    public $election_city_lookup_table  = 'i_ma_election_city_lookup';

    public $elections_lookup             = [];


    ////////////////////////////////////////////////////////////////////////////////
    //
    // REQUIRED FUNCTIONS:
    //

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // if (config('app.env') != 'local') dd('Cannot run in live yet.');

        $file_path_elections      = ($this->option('file_path_elections')) ?: null;
        $file_path_election_voter = ($this->option('file_path_election_voter')) ?: null;
        $table_lookup_cities      = ($this->option('table_lookup_cities')) ?: null;
        $table_add_elections      = ($this->option('table_add_elections')) ?: null;

        if ($this->option('clear_elections'))      $this->truncateIfExists($this->elections_table);
        if ($this->option('clear_election_voter')) $this->truncateIfExists($this->pivot_table);

        //----------------------------------------------------------------------------------
        // Elections

        $has_elections = $this->checkHasTableAndNotEmpty($this->elections_table);

        if (!$file_path_elections && !$has_elections) {
            $this->info('Election table needed');
            $file_path_elections = $this->selectFilePath($this->elections_storage_subdir, 
                                                         $what_for = 'ELECTIONS');
            // $file_path_election_voter = $this->selectFilePath($this->elections_storage_subdir, 
            //                                          $what_for = 'ELECTIONS/VOTER PIVOT FOR LOOKUP');
        }

        //----------------------------------------------------------------------------------
        // Elections-Voter Pivot

        $has_pivot = $this->checkHasTableAndNotEmpty($this->pivot_table);

        if (!$file_path_election_voter && !$has_pivot) {
            $this->info('Election-voter pivot table needed');
            $file_path_election_voter = $this->selectFilePath($this->elections_storage_subdir, 
                                                     $what_for = 'ELECTIONS/VOTER PIVOT');
        }

        //----------------------------------------------------------------------------------
        // Elections (PROCESS)

        if (!$has_elections) {
            $this->uploadElectionFile($file_path_elections);
            $this->elections_lookup = ImportedMAElection::all()->keyBy('election_id');

            //---------------------------------------------------------------------------
            // Elections-City Pivot

            // if (!$file_path_election_voter) {
            //     $file_path_election_voter = $this->selectFilePath($this->elections_storage_subdir,
            //         $what_for = 'ELECTIONS/VOTER PIVOT FOR LOOKUP');
            // }

            // $this->uploadElectionCity($file_path_election_voter);

            // if ($table_lookup_cities) {

            //     if ($table_lookup_cities = '{CURRENT MASTER}') {
            //         $this->table_name = $this->getMaster($this->state);
            //     } elseif ($table_lookup_cities = '{MOST RECENT MASTER}') {
            //         $this->table_name = $this->getMostRecentMaster($this->state);
            //     } else {
            //         $this->table_name = $table_lookup_cities;
            //     }

            // } else {

            //     $this->table_name = $this->pickVoterTable('To determine cities...');

            // }

            //$this->getMostCommonCityForEachElection();

        }

        //---------------------------------------------------------------------------
        // Elections-Voter Pivot Process (PROCESS)

        if (!$has_pivot) {
            $this->elections_lookup = ImportedMAElection::all()->keyBy('election_id');
            $this->uploadPivotFile($file_path_election_voter);
        }

        //----------------------------------------------------------------------------------
        // Append Elections to Voter (Ask which Master Table + PROCESS)

        if ($table_add_elections) {

            // if ($table_add_elections = '{CURRENT MASTER}') {
            //     $this->table_name = $this->getMaster($this->state);
            // } else
            if ($table_add_elections = '{MOST RECENT MASTER}') {
                $this->table_name = $this->getMostRecentMaster($this->state);
            } else {
                $this->table_name = $table_add_elections;
            }

        } else {

            $this->table_name = $this->pickVoterTable('For appending elections info...',
                                                      $allow_current_master = false);

        }

        
        $this->appendElectionsToEachVoter();
    }


    public function forEachRow($switch, $row, $row_num)
    {
        switch ($switch) {
            case 'elections':
                return $this->importElectionRow($row, $row_num);
                break;

            case 'election_voter':
                return $this->importPivotRow($row, $row_num);
                break;

            case 'election_city':
                return $this->importElectionCityRow($row, $row_num);
                break;

        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // MASS. ELECTIONS FUNCTIONS:
    //

    public function pickVoterTable($description = null, $allow_current_master = null)
    {
        echo "\n";
        if ($description) $this->info($description);

        $master_choices = [];
        
        if ($this->getListOfPreviousMasters()->first()) {
            $master_choices['pick one'] = 'A previously-imported Master table';
        }
        if ($allow_current_master !== false && $this->getMaster($this->state)) {
            $master_choices['current'] = 'The current Master table';
        }

        if (count($master_choices) > 1) {
            $this->rekeyStartingAtOne($master_choices);
            $which_master = $this->choice('Use which Master Table?', $master_choices);
        } else {
            $which_master = array_key_first($master_choices);
        }

        if ($which_master == 'pick one')    $selection = $this->selectPreviousMaster();
        if ($which_master == 'current')     $selection = $this->getMaster($this->state);

        return $selection;
    }

    ////////////////////////////////////////////////////////////////////////////////

    // public function uploadElectionCity($file_path)
    // {
    //     echo "\n";
    //     $this->info('Populating elections->voters (for Cities Lookup)...');

    //     $this->expected_num_rows  = $this->expectedNumRows($file_path);
    //     $this->delimiter    = $this->detectDelimiter($file_path);
    //     $this->firstrow     = $this->getFirstRow($file_path);
    //     $this->start_time   = Carbon::now();

    //     $log                = $this->createErrorLog($name = 'election_city');

    //     $this->openHandleAndGoThrough($file_path,
    //                                   $switch = 'election_city',
    //                                   $log);

    //     $this->saveElectionsLookup();
    // }

    // public function saveElectionsLookup()
    // {
    //     echo "\n";
    //     $this->info('Saving....');

    //     $this->expected_num_rows = count($this->elections_lookup);

    //     $insert = 0;
    //     foreach($this->elections_lookup as $election) {
    //         $election->save();  
    //         echo $this->progress($insert++)."\r";
    //     }
    // }    

    // public function getMostCommonCityForEachElection()
    // {
    //     echo "\n";
    //     $this->info('Getting most common city per election....');

    //     // First, remove any old data from elections table
    //     DB::connection('voters')->statement('UPDATE '.$this->elections_table.' SET voters=NULL, voter_count=0');

    //     session(['table_while_importing_master' => $this->table_name]);

    //     $row = 0;

    //     $could_not_find     = [];
    //     $sample_too_small   = [];
    //     $missing_voters     = [];
    //     $success            = [];

    //     $this->expected_num_rows = count($this->elections_lookup);
    //     $this->start_time = Carbon::now();

    //     $minimum_sample_size = 5;

    //     foreach($this->elections_lookup as $election) {

    //         $row++;

    //         echo $this->progress($row)."\r";

    //         if (empty($election->voters)) {
    //             $missing_voters[] = $election->election_id;
    //             continue;
    //         }
    //         if ($election->voter_count < $minimum_sample_size) {
    //             $sample_too_small[] = $election->election_id;
    //             continue;
    //         }

    //         // $voters_sample = json_decode($election->voters);
    //         $voters_sample = $election->voters;

    //         $voter_ids = [];
    //         foreach($voters_sample as $id) {
    //             $voter_ids[] = $this->state.'_'.$id;
    //         }

    //         $cities = ImportedVoterMaster::whereIn('id', $voter_ids)
    //                              ->whereNotNull('city_code')
    //                              ->pluck('city_code')
    //                              ->toArray();

    //         // if ($names) print_r($cities);

    //         $codes = array_count_values($cities);
    //         arsort($codes);
    //         $most_common_code = array_key_first($codes); // This was wrong: array_shift($codes);

    //         $city = Municipality::where('state', $this->state)
    //                             ->where('code', $most_common_code)
    //                             ->first();

    //         if ($city) {

    //             $election->city = $city->name;
    //             $election->cf_code = $city->code;
    //             $election->save();

    //             $success[] = $election->election_id;

    //         } else {

    //             $could_not_find[] = $election->election_id;

    //         }

            
    //     }

    //     echo "\n";
    //     $total = count($success)
    //              + count($could_not_find)
    //              + count($missing_voters)
    //              + count($sample_too_small);

    //     $this->info(
    //             'SUCCESS:             '.number_format(count($success)).
    //                                   "\t ----> ".number_format(count($success)/$total *100)."%\n".
    //             'COULD NOT FIND CITY: '.number_format(count($could_not_find))."\n".
    //             'MISSING VOTERS:      '.number_format(count($missing_voters))."\n".
    //             'SAMPLE TOO SMALL:    '.number_format(count($sample_too_small))."\n".
    //             'TOTAL:               '.number_format($total)
    //            );

    //     echo "\n";
    // }


    // public function importElectionCityRow($row, $row_num)
    // {
    //     $stop_counting_at = 100;

    //     $csv = $this->englishColumnNames($row, $this->firstrow);

    //     if (!isset($this->elections_lookup[$csv['ElectionID']])) return;

    //     $election = $this->elections_lookup[$csv['ElectionID']];

    //     if ($election && $election->local) {

    //         if ($stop_counting_at && $election->voter_count >= $stop_counting_at) {
    //             return;
    //         }

    //         $existing = (array) $election->voters;
    //         $existing[] = $csv['StateVoterID'];
    //         $election->voters = $existing;

    //         $election->voter_count++;

    //     }
    // }

    ////////////////////////////////////////////////////////////////////////////////

    public function uploadElectionFile($file_path)
    {
        echo "\n";
        $this->info('Uploading Elections...');

        Schema::connection('voters')->dropIfExists($this->elections_table);
        Schema::connection('voters')->create($this->elections_table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('election_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();
            $table->boolean('local')->default(0);
            $table->string('county')->nullable();
            $table->unsignedInteger('fips')->nullable();
            $table->string('code')->nullable();
            // $table->unsignedInteger('voter_count')->default(0);
            // $table->text('voters')->nullable();
            // $table->string('city')->nullable();
            // $table->unsignedInteger('cf_code')->nullable();            
            $table->timestamps();
        });

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter    = $this->detectDelimiter($file_path);
        $this->firstrow     = $this->getFirstRow($file_path);
        $this->start_time   = Carbon::now();

        $log                = $this->createErrorLog($name = $this->elections_table);

        echo "\n";

        $this->openHandleAndGoThrough($file_path,
                                      $switch = 'elections',
                                      $log);

    }

    public function importElectionRow($row, $row_num)
    {
        $csv = $this->englishColumnNames($row, $this->firstrow);

        // "ElectionID" => "318090"
        // "ElectionName" => "LOCAL ELECTION"
        // "ElectionDate" => "04/27/2002"
        // "ElectionType" => "Local or Municipal"
        // "County" => "Bristol"
        // "FIPS" => "005"
        // "ElectionCode" => "LOCAL ELECTION|04/27/2002"

        $election = new ImportedMAElection;

        $election->election_id      = $csv['ElectionID'];
        $election->name             = $csv['ElectionName'];
        $election->date             = Carbon::parse($csv['ElectionDate'])->toDateString();
        $election->type             = $csv['ElectionType'];
        $election->county           = $csv['County'];
        $election->fips             = $csv['FIPS'];
        $election->code             = $csv['ElectionCode'];
        $election->local            = (substr($election->type, 0, 1) == 'L');

        $election->save();
    }

    ////////////////////////////////////////////////////////////////////////////////

    public function uploadPivotFile($file_path)
    {
        echo "\n";
        $this->info('Uploading Elections-Voter Pivot...');

        Schema::connection('voters')->dropIfExists($this->pivot_table);
        Schema::connection('voters')->create($this->pivot_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cf_voter_id')->nullable()->index();
            $table->unsignedInteger('rec_id')->nullable();
            $table->string('voter_id')->nullable()->index();
            $table->string('county_voter_id')->nullable();
            $table->unsignedInteger('election_id')->nullable();
            $table->string('ballot_party')->nullable();
            $table->string('ballot_type')->nullable();
            $table->string('ballot_type_code')->nullable();
            $table->date('ballot_return_date')->nullable();
            $table->string('election_name')->nullable();
            $table->date('election_date')->nullable();
            $table->string('election_type')->nullable();
            $table->string('local')->nullable();
            // $table->string('cf_election_id_city')->nullable();
            $table->string('cf_election_id_key')->nullable();
            $table->string('cf_election_id_value')->nullable();
            $table->timestamps();
        });

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter    = $this->detectDelimiter($file_path);
        $this->firstrow     = $this->getFirstRow($file_path);
        $this->start_time   = Carbon::now();

        $log                = $this->createErrorLog($name = $this->pivot_table);

        $this->massInsert($file_path,
                          $switch = 'election_voter',
                          $log,
                          500);
    }

    public function importPivotRow($row, $row_num)
    {
        $csv = $this->englishColumnNames($row, $this->firstrow);

        // "RECID" => "0"
        // "StateVoterID" => "09CWN2287000"
        // "CountyVoterID" => ""
        // "ElectionID" => "652419"
        // "BallotParty" => ""
        // "BallotType" => ""
        // "BallotTypeCode" => ""
        // "BallotReturnDate" => ""

        $pivot = new ImportedMAElectionVoter;

        $pivot->cf_voter_id          = $this->state.'_'.$csv['StateVoterID'];
        $pivot->rec_id               = $csv['RECID'];
        $pivot->voter_id             = $csv['StateVoterID'];
        $pivot->county_voter_id      = ($csv['CountyVoterID'] == '') ? null : $csv['CountyVoterID'];
        $pivot->election_id          = $csv['ElectionID'];
        $pivot->ballot_party         = ($csv['BallotParty'] == '') ? null : $csv['BallotParty'];
        $pivot->ballot_type          = ($csv['BallotType'] == '') ? null : $csv['BallotType'];
        $pivot->ballot_type_code     = ($csv['BallotTypeCode'] == '') ? null : $csv['BallotTypeCode'];
        $pivot->ballot_return_date   = (!$csv['BallotReturnDate']) ? null 
                                            : Carbon::parse($csv['BallotReturnDate'])->toDateString();
        if (isset($this->elections_lookup[$pivot->election_id])) {
            $election = $this->elections_lookup[$pivot->election_id];
            $pivot->election_name       = $election->name;
            $pivot->election_date       = $election->date;
            $pivot->election_type       = $election->type;
            // $pivot->cf_election_id_city = $election->cf_code;
            $pivot->local               = $election->local;
        }

        if ($cf_election_id = $this->formatElectionArray($pivot)) {
            $pivot->cf_election_id_key      = key($cf_election_id);
            $pivot->cf_election_id_value    = array_shift($cf_election_id);
        }

        return $pivot;

    }

    public function formatElectionArray($participation)
    {
        // {"MA-1999-03-08-L0000-0191":"0191-U-0",
        // "MA-2000-11-07-STATE-0000":"0191-U-0",
        // "MA-2004-11-02-STATE-0000":"0061-U-0"}

        // election_type
        // -------------------------
        // General
        // Local or Municipal
        // Presidential Primary
        // Primary
        // Special

        // election_name
        // -------------------------
        // GENERAL
        // GENERAL ELECTION
        // LOCAL ELECTION
        // LOCAL REP TOWN MTG
        // LOCAL SPECIAL
        // LOCAL TOWN MEETING
        // NOVEMBER SPECIAL
        // PRESIDENTIAL PRIMARY
        // PRIMARY
        // SPECIAL STATE
        // SPECIAL STATE PRIMARY
        // SPECIAL US SENATE STATE PRIMARY
        // STATE ELECTION
        // STATE PRIMARY

        // CF:
        // -------------------------
        // 'Local Election' => 'L',
        // 'Presidential Primary' => 'PP',
        // 'State Primary' => 'SP',
        // 'State Election' => ' STATE',
        // 'Local Primary' => 'LP',
        // 'Special State' => 'SS',
        // 'Primary Election' => 'PE',    <----------- Not actually used by CF?
        // 'General Election' => 'G',     <----------- Not actually used by CF?
        // 'Local Special' => 'LS',
        // 'Special State Primary' => 'SSP',
        // 'Local Town Meeting' => 'LTM',
        // 'Local Rep Town Mtg' => 'LRTM',

        $map = ['GENERAL'               => 'STATE',
                'GENERAL ELECTION'      => 'STATE',
                'LOCAL ELECTION'        => 'L',
                'LOCAL REP TOWN MTG'    => 'LRTM',
                'LOCAL SPECIAL'         => 'LS',
                'LOCAL TOWN MEETING'    => 'LTM',
                'NOVEMBER SPECIAL'      => 'SS',
                'PRESIDENTIAL PRIMARY'  => 'PP',
                'PRIMARY'               => 'SP',
                'SPECIAL STATE'         => 'SS',
                'SPECIAL STATE PRIMARY' => 'SSP',
                'STATE ELECTION'        => 'STATE',
                'STATE PRIMARY'         => 'SP'
               ];

        if (isset($map[$participation->election_name])) {
            $type = $map[$participation->election_name];
        } else {
            $type = null;
        }
        
        //$city = $participation->cf_election_id_city;
        $city = null;
        if ($participation->local && !$city) $city = 'need';

        $key = $this->state.'-'
                .Carbon::parse($participation->election_date)->toDateString().'-'
                .str_pad($type,  5, "0").'-'
                .str_pad($city,  4, "0", STR_PAD_LEFT); 

        $value = str_pad($city,  4, "0", STR_PAD_LEFT).'-'
                .'0'.'-' // Party registration at the time
                .str_pad($this->formatParty($participation->ballot_party), 1, "0"); // Took ballot
                

        return [$key => $value];
    }

    ////////////////////////////////////////////////////////////////////////////////

    public function appendElectionsToEachVoter()
    {
        echo "\n";
        $this->info('Appending elections into: '.$this->table_name.'...');

        session(['table_while_importing_master' => $this->table_name]);

        echo 'Counting...';

        $this->expected_num_rows    = ImportedVoterMaster::withTrashed()->count();
        echo $this->expected_num_rows."\n";

        $this->start_time           = Carbon::now();
        $row = 0;
        $skip = 0;
        $chunk = 250;
        
        ImportedVoterMaster::chunkById($chunk, function($voters) use (&$row) {

            $elections = ImportedMAElectionVoter::whereIn('cf_voter_id', $voters->pluck('id'))
                                                ->get()
                                                ->groupBy('cf_voter_id');

            foreach ($voters as $voter) {

                echo $this->progress($row++)."\r";

                if (!isset($elections[$voter->id])) {
                    continue;
                }

                $participations = $elections[$voter->id];

                if (!$participations->first()) continue;

                $array = [];
                foreach($participations as $participation) {
                    
                    $array = array_merge($array, 
                                                [$participation->cf_election_id_key =>
                                                 $participation->cf_election_id_value]
                                        );

                }

                ksort($array);
                $voter->elections = (!empty($array)) ? $array : null;
                $voter->save();

            }

        });

    }

    
}
