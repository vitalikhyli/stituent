<?php

namespace App\Console\Commands;

use App\Election;
use App\ElectionProfile;
use App\ElectionRange;
use App\Voter;
use App\VoterMaster;
use App\VoterProcessing;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class Elections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:elections {--state=} {--slice=} {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates ElectionRange and ElectionProfile for the given state';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->current_year = 2020; //Carbon::now()->format("Y");

        $this->define_range = [$this->current_year,
                                  $this->current_year - 2,
                                  $this->current_year - 4,
                                  $this->current_year - 6,
                                  $this->current_year - 8, ];

        //
        // DESIGN NOTES
        //

        //          local   State           local   State
        // ----------------------------------------------
        // Start Even Year          Start Odd Year
        // ----------------------------------------------
        // 2020             1 Prez
        // 2019___  1               2019    1
        // 2018             2 Gub   2018___         1 Gub
        // 2017___  2               2017    2
        // 2016             3 Prez  2016___         2 Prez
        // 2015___  3               2015    3
        // 2014             4 Gub   2014___         3 Gub
        // 2013___  4               2013    4
        // -------------------------2012___         4 Prez <-- 8 ys always gets 4 of each type
        //                                                      (or 2 President / Governor)
        //

        $this->start_range = $this->define_range;
        unset($this->start_range[0]);

        $this->longest_range = substr(min($this->start_range), 2, 2)
                                 .substr($this->current_year, 2, 2);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public $VOTERS_MODEL;

    public function handle()
    {
        if (! $this->option('state')) {
            dd('Needs state, jerk, like cf:elections --state=MA');
        }
        $state = $this->option('state');
        session()->put('team_state', $state);

        $profile_table = $state.'_election_profiles';
        $range_table = $state.'_election_ranges';
        $processing_table = $state.'_voters_processing';
        if ($state != 'MA') {
            DB::connection('voters')->statement('CREATE TABLE IF NOT EXISTS `'.$profile_table.'` LIKE MA_election_profiles');
            DB::connection('voters')->statement('CREATE TABLE IF NOT EXISTS `'.$range_table.'` LIKE MA_election_ranges');
            DB::connection('voters')->statement('CREATE TABLE IF NOT EXISTS `'.$processing_table.'` LIKE MA_voters_processing');
        }

        $at_a_time = 1000;
        $go = true;
        $chunk_increment = 0;
        $artificial_limit = 10000000;

        if ($this->option('slice')) {
            $slice = $this->option('slice');
            session()->put('team_table', $slice);
            $this->VOTERS_MODEL = Voter::class;
        } else {
            $this->VOTERS_MODEL = VoterMaster::class;
            $slice = 'x_voters_'.$state.'_master';
            if ($state == 'XX') {
                $slice = 'x_voters_MA_master';
            }
        }

        $total = $artificial_limit;
        // dd(Voter::all());
        // $current_year = 2020;

        $this->info('Slice: '.$slice);

        $lastweek = Carbon::today()->subWeek();

        while ($go == true) {

            // DB::enableQueryLog();

            // try {
            
            //     $voters_query = $this->VOTERS_MODEL::query();
            //     $voters_query->selectRaw($slice.'.*, '.$processing_table.'.elections_processed_at')
            //                  ->leftJoin(config('database.connections.voters.database').'.'.$processing_table, $slice.'.id', '=', $processing_table.'.voter_id');

            //     if ($this->option('update')) {
            //         $voters_query->where($processing_table.'.elections_processed_at', '<', $lastweek->format('Y-m-d'));
            //     } else {
            //         $voters_query->whereNull($processing_table.'.elections_processed_at');
            //     }
            //     $voters_query->with('profile', 'range', 'processing')
            //                  ->take($at_a_time);

            //     echo "About to get ".$at_a_time." voters.\n";
            //     echo $voters_query->toSql()."\n";
            //     $voters = $voters_query->get();
            // } catch (\Exception $e) {
            //     dd(DB::getQueryLog());
            // }

            
            $voters_sql = "select $slice.*, MA_voters_processing.elections_processed_at 
                from `$slice` 
                left join `fluency_voters`.`MA_voters_processing` 
                on `$slice`.`id` = `MA_voters_processing`.`voter_id` 
                where `$slice`.`deleted_at` is null 
                and `MA_voters_processing`.`elections_processed_at` < '".$lastweek->format('Y-m-d')."' 
                limit $at_a_time";

            echo $voters_sql."\n";
            $voters = DB::select(DB::raw($voters_sql));
            
            $voters = $this->VOTERS_MODEL::hydrate($voters);
            //dd($voters);

            //dd($voters);
            echo $voters->count()." queried.\n";
            //dd($voters);
            $chunk_increment++;

            $voter_count = 0;

            if (! $voters->first() || ($artificial_limit < $chunk_increment * $at_a_time)) {
                echo "\r\nDone\r\n";
                $go = false;
            } else {
                echo "About to process ".$voters->count()." voters.\n";
                foreach ($voters as $voter) {

                    //dd($voter);

                    if (! $voter->id) {
                        echo "Voter has no ID\n";
                        continue;
                    }

                    //dd($voter);

                    $elections = $this->getVoterElectionsAndParse($voter);

                    foreach ($this->start_range as $start) {
                        $counts = $this->getQueryCounts($elections, $start, $this->current_year);
                        $this->insertQueryCounts($voter, $counts, $start, $this->current_year);
                    }

                    $profile = $this->createProfile($voter);
                    $this->insertProfile($voter, $profile);

                    $processing = $voter->processing;
                    if (! $voter->processing) {
                        $processing = new VoterProcessing;
                        $processing->voter_id = $voter->id;
                    }
                    $processing->elections_processed_at = Carbon::now();
                    $processing->save();
                    //dd($processing);
                }
            }

            // Progress Bar

            $current_record = ($chunk_increment - 1) * $at_a_time + $voter_count++ + 1;
            //echo 'Election Counts: '.implode(' ', $counts)."\r\n";
            $percent_complete = round(($current_record / $total * 100), 0);
            $bar_length = $percent_complete * .01 * 50;
            echo str_repeat('*', $bar_length);
            if (50 - $bar_length > 0) {
                echo str_repeat('.', 50 - $bar_length);
            }
            echo ' '.number_format($current_record).' '.$percent_complete.'%'."\r\n";
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //
    // PROFILES
    //

    public function createProfile($voter)
    {
        $voter = $this->VOTERS_MODEL::find($voter->id); // Other code not using ELoquent
        $profile = [];

        $long = $this->longest_range;

        //////////////////////////////////////// SUPER VOTERS
        $field = $long.'_local_any';
        $profile['stalwart_local'] = ($voter->range->$field >= 4) ? true : false;

        $field = $long.'_state_any';
        $profile['stalwart_state'] = ($voter->range->$field >= 4) ? true : false;

        //////////////////////////////////////// RELAIBLE

        $field = $long.'_local_any';
        $profile['reliable_local'] = ($voter->range->$field >= 3) ? true : false;

        $field = $long.'_state_any';
        $profile['reliable_state'] = ($voter->range->$field >= 3) ? true : false;

        //////////////////////////////////////// MEDIUM
        $field = $long.'_local_any';
        $profile['somewhat_local'] = ($voter->range->$field >= 2) ? true : false;

        $field = $long.'_state_any';
        $profile['somewhat_state'] = ($voter->range->$field >= 2) ? true : false;

        //////////////////////////////////////// OTHER

        $profile['recently_registered'] = (Carbon::parse($voter->registration_date)->diffInDays() <= 365) ? true : false;

        //////////////////////////////////////// PRIMARY BALLOTS
        //dd($voter->elections);
        /*

            {"MA-2006-11-07-STATE-0000":"0274-U-0","MA-2007-11-06-L0000-0274":"0274-U-0","MA-2008-02-05-PP000-0000":"0274-U-D","MA-2008-09-16-SP000-0000":"0274-U-D","MA-2008-11-04-STATE-0000":"0274-U-0","MA-2009-11-03-L0000-0274":"0274-U-0","MA-2009-12-08-SP000-0000":"0274-U-D","MA-2010-01-19-SS000-0000":"0274-U-0","MA-2010-09-14-SP000-0000":"0274-U-D","MA-2010-11-02-STATE-0000":"0274-U-0","MA-2011-11-08-L0000-0274":"0274-U-0","MA-2012-11-06-STATE-0000":"0274-U-0","MA-2013-04-30-SSP00-0000":"0274-U-D","MA-2013-06-25-SS000-0000":"0274-U-0","MA-2013-09-24-L0000-0274":"0274-U-0","MA-2013-11-05-L0000-0274":"0274-U-0","MA-2014-09-09-SP000-0000":"0243-U-D","MA-2014-11-04-STATE-0000":"0243-U-0","MA-2016-03-01-PP000-0000":"0011-U-D","MA-2016-11-08-STATE-0000":"0011-U-0","MA-2017-06-20-L0000-0011":"0011-U-0","MA-2017-07-18-LTM00-0011":"0011-U-0","MA-2017-08-08-L0000-0011":"0011-U-0","MA-2018-09-04-SP000-0000":"0011-U-D"}

        */
        if ($voter->elections) {
            $primary_2016 = 'MA-2016-03-01-PP000-0000';
            if (isset($voter->elections[$primary_2016])) {
                $city_reg_ball = $voter->elections[$primary_2016];
                $crb_arr = explode('-', $city_reg_ball);
                $ballot = $crb_arr[2];
                $profile['primary_ballot_2016'] = $ballot;
            }

            $primary_2018 = 'MA-2018-09-04-SP000-0000';
            if (isset($voter->elections[$primary_2018])) {
                $city_reg_ball = $voter->elections[$primary_2018];
                $crb_arr = explode('-', $city_reg_ball);
                $ballot = $crb_arr[2];
                $profile['primary_ballot_2018'] = $ballot;
            }

            $primary_2020 = 'MA-2020-09-01-SP000-0000'; //The Sep 1st Primary
            if (isset($voter->elections[$primary_2020])) {
                $city_reg_ball = $voter->elections[$primary_2020];
                $crb_arr = explode('-', $city_reg_ball);
                $ballot = $crb_arr[2];
                $profile['primary_ballot_2020'] = $ballot;
            }
        }

        return $profile;
    }

    public function insertProfile($voter, $data)
    {
        $profile = $voter->profile;

        if (! $profile) {
            $profile = new ElectionProfile;
            $profile->voter_id = $voter->id;
        }

        //$this->info($voter->id);
        foreach ($data as $field => $value) {
            $profile->$field = $value;
            //echo $field.' = '.$value."\n";
        }

        $profile->save();
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    //
    // RANGES
    //

    public function insertQueryCounts($voter, $counts, $start, $end)
    {
        $range = $voter->range;

        if (! $range) {
            $range = new ElectionRange;
            $range->voter_id = $voter->id;
        }

        foreach ($counts as $field => $count) {
            $range->$field = $count;
        }

        try {
            $range->save();
            $voter->range = $range;
        } catch (\Exception $e) {
            dd($voter, $range);
        }
    }

    public function getQueryCounts($election, $start, $end)
    {
        $queries = [
        'any'                           => [],
        'state_any'                     => ['state'],
        'state_general'                 => ['state', 'general'],
        'state_general_gub'             => ['state', 'general', 'gubernatorial'],
        'state_primary'                 => ['state', 'primary'],
        'state_primary_gub'             => ['state', 'primary', 'gubernatorial'],
        'state_special_general'         => ['state', 'general', 'special'],
        'state_special_primary'         => ['state', 'primary', 'special'],
        'local_any'                     => ['local'],
        'local_general'                 => ['local', 'general'],
        'local_preliminary'             => ['local', 'primary'],
        // 'local_special_general'         => ['local', 'general', 'special'], // NOT USED NOW
        // 'local_special_preliminary'     => ['local', 'primary', 'special'], // NOT USED NOW
        'local_town_meeting'            => ['local', 'town_meeting'],
        'state_presidential_primary'    => ['state', 'prez_primary'],
        ];

        $range = substr($start, 2, 2).substr($end, 2, 2).'_';

        $results = [];

        foreach ($queries as $query => $fields) {            // Loop 1

            $results[$range.$query] = 0;

            foreach ($election as $key => $data) {           // Loop 2

                if ($data['year'] >= $start && $data['year'] <= $end) {
                    foreach ($fields as $needed) {               // Loop 3
                        if ($data[$needed] != true) {
                            continue 2;
                        } //Continues to next ELECTION loop
                    }

                    $results[$range.$query]++;
                }
            }
        }

        return $results;
    }

    public function getVoterElectionsAndParse($voter)
    {
        $elections = [];
        if ($voter->elections) {
            if (!is_array($voter->elections)) {
                $voter->elections = json_decode($voter->elections);
            }

            foreach ($voter->elections as $election => $participation) {
                $elections[$election] = $this->parseElection($election);
            }
            
        }

        return $elections;
    }

    public function parseElection($string)
    {
        $components = explode('-', $string);

        // MA-2000-11-07-STATE-0000
        // MA-2009-11-03-L0000-0035

        $data = [];
        $type = $components[4];

        // Basic Data
        $data['jurisdiction'] = $components[0]; // Because "State" ambigious
        $data['year'] = $components[1] * 1;
        $data['date'] = $components[1].'-'.$components[2].'-'.$components[3];
        if (is_numeric($components[5])) {
            $data['city_code'] = $components[5] * 1;
        } else {
            $data['city_code'] = 0;
        }

        // Character 1
        $data['local'] = (substr($type, 0, 1) == 'L') ? true : false;
        $data['prez_primary'] = (substr($type, 0, 2) == 'PP') ? true : false;

        // Character 2
        $data['special'] = (substr($type, 1, 1) == 'S') ? true : false;
        $data['town_meeting'] = (substr($type, 1, 2) == 'TM') ? true : false;

        // Primary can be in two places
        $data['primary'] = false;
        if (substr($type, 0, 2) == 'SP') {
            $data['primary'] = true;
        }
        if (substr($type, 0, 3) == 'SSP') {
            $data['special'] = true;
            $data['primary'] = true;
        }
        if (substr($type, 0, 2) == 'LP') {
            $data['primary'] = true;
        }
        if (substr($type, 0, 2) == 'LS') {  // <---------------- CURRENTLY NOT USED?
            $data['special'] = true;
        }
        if (substr($type, 0, 3) == 'LSP') { // <---------------- CURRENTLY NOT USED?
            $data['special'] = true;
            $data['primary'] = true;
        }

        // Based on Other Data
        $data['general'] = (! $data['primary']) ? true : false;
        $data['state'] = (! $data['local']) ? true : false;

        // Based on Year
        $data['gubernatorial'] = ($data['state'] && ($data['year'] - 2) % 4 == 0) ? true : false;
        $data['presidential'] = ($data['state'] && $data['year'] % 4 == 0) ? true : false;

        return $data;
    }
}
