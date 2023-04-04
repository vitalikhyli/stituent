<?php

namespace App\Traits;

use App\CampaignList;
use App\CampaignParticipant;
use App\District;
use App\GroupPerson;
use App\Municipality;
use App\Participant;
use App\ParticipantTag;
use App\UserUploadData;
use App\UserUpload;
use App\Voter;
use App\Action;

use Auth;
use Carbon\Carbon;
use App\CFPlus;

trait ListBuilderTrait
{
    public function getCategories()
    {
        $categories = collect([]);

        $university_team = Auth::user()->university_team;
        $office_team = Auth::user()->office_team;

        if ($university_team) {

            $categories = $university_team->categories()->get();

        } elseif ($office_team) {

            $categories = $office_team->categories()->get();

        }

        return $categories;
    }

    public function getElectionPretty($election_id)
    {

        $election_arr = explode('-', $election_id);
        $year = $election_arr[1];
        $month = $election_arr[2];
        $day = $election_arr[3];
        $date = $month.'/'.$day.'/'.$year;
        $type = "";
        switch ($election_arr[4]) {

            case 'L0000':
                $city_id = $election_arr[5];
                $city = Municipality::find($city_id);
                $jurisdiction = ($city) ? $city->name : null;
                $type = 'Municipal';
                break;

            case 'LTM00':
                $city_id = $election_arr[5];
                $city = Municipality::find($city_id);
                $jurisdiction = ($city) ? $city->name : null;
                $type = 'Town Meeting';
                break;

            case 'STATE':
                $jurisdiction = 'State';
                $type = 'General';
                break;

            case 'PP000':
                $jurisdiction = 'State';
                $type = 'Presidential Primary';
                break;

            case 'SP000':
                $jurisdiction = 'State';
                $type = 'Primary';
                break;

            case 'SS000':
                $jurisdiction = 'State';
                $type = 'Special';
                break;

            case 'SSP00':
                $jurisdiction = 'State';
                $type = 'Special Primary';
                break;

            case 'L0000':
                $jurisdiction = 'District';
                // $district_id = $election_arr[5];
                // $district = District::find($district_id);
                // $type = "District ".$district->name;
                $type = 'Legislative ';
                break;

            case 'LS000':
                $jurisdiction = 'District';
                // $district_id = $election_arr[5];
                // $district = District::find($district_id);
                // $type = "District ".$district->name." special";
                $type = 'Legislative Special';
                break;

            default:
                $jurisdiction = null;
                //$type = implode('-', $election_id);
                break;
        }

        $election = $date." - ".$jurisdiction." ".$type;
        //dd($election);
        return $election;
    }

    public function getMunicipalities()
    {
        $city_codes =  Voter::withTrashed()
                            ->whereNull('archived_at')
                            ->groupBy('city_code')
                            ->pluck('city_code');

        if (Auth::user()->app_type == 'campaign') {
            $participant_city_codes = Participant::where('team_id', Auth::user()->team->id)
                                                 ->distinct()
                                                 ->select('city_code')
                                                 ->pluck('city_code');
            $city_codes = $city_codes->merge($participant_city_codes)->unique();
        }

        $municipalities = Municipality::whereIn('code', $city_codes)
                                      ->where('state', session('team_state'))
                                      ->orderBy('name')
                                      ->get();

        return $municipalities;
    }
    public function getEthnicities()
    {
        return CFPlus::groupBy('ethnic_description')->orderBy('ethnic_description')->pluck('ethnic_description');
    }
    public function getZipCodes()
    {
        $zipcodes = Voter::withTrashed()->distinct()->select('address_zip')
                                                    ->pluck('address_zip')
                                                    ->toArray();

        return $zipcodes;
    }

    public function getHouseDistricts()
    {
        $district_ids = Voter::withTrashed()
                             ->distinct()
                             ->select('house_district')
                             ->pluck('house_district')
                             ->unique();

        return District::where('type', 'H')
                        ->where('state', session('team_state'))
                        ->whereIn('code', $district_ids)
                        ->orderBy('sort')
                        ->get();
    }

    public function getSenateDistricts()
    {
        $district_ids = Voter::withTrashed()
                             ->distinct()
                             ->select('senate_district')
                             ->pluck('senate_district')
                             ->unique();

        return District::where('type', 'S')
                        ->where('state', session('team_state'))
                        ->whereIn('code', $district_ids)
                        ->orderBy('sort')
                        ->get();
    }

    public function getCongressionalDistricts()
    {
        $district_ids = Voter::withTrashed()
                             ->distinct()
                             ->select('congress_district')
                             ->pluck('congress_district')
                             ->unique();

        return District::where('type', 'F')
                        ->where('state', session('team_state'))
                        ->whereIn('code', $district_ids)
                        ->orderBy('sort')
                        ->get();
    }

    public function getSampleElections()
    {
        $elections = [];
        //$random_100 = Voter::inRandomOrder()->take(200)->get();
        $random_100 = Voter::take(200)->get();
        foreach ($random_100 as $v) {
            $voter_elections = $v->elections;
            if (!$voter_elections) {
                continue;
            }
            foreach ($voter_elections as $election_id => $voter_election) {
                if (isset($elections[$election_id])) {
                    $elections[$election_id] = $elections[$election_id]+1;
                } else {
                    $elections[$election_id] = 1;
                }
            }
        }
        foreach ($elections as $election_id => $ecount) {
            $year = substr($election_id, 3, 4);
            if ((int)$year < 2012) {
                unset($elections[$election_id]);
                continue;
            }
            if ($ecount < 3) {
                unset($elections[$election_id]);
                continue;
            }
            $elections[$election_id] = (int) ($ecount / 2);
        }
        krsort($elections);
        //dd($elections);

        $new_elections  = [];
        foreach ($elections as $election_id => $election_count) {
            //echo $this->getElectionPretty($election_id)."<br>";
            $new_elections[$election_id]['name'] = $this->getElectionPretty($election_id);
            $new_elections[$election_id]['count'] = $election_count;
        }
        //dd($new_elections);
        krsort($new_elections);
        //dd($new_elections);
        return $new_elections;
    }

    public function getAllDistricts()
    {
        $f_districts = $this->getCongressionalDistricts();
        $s_districts = $this->getSenateDistricts();
        $h_districts = $this->getHouseDistricts();
        $districts = $f_districts->merge($s_districts->merge($h_districts));

        return $districts;
    }

    public function getZips()
    {
        $zips = Voter::withTrashed()->distinct()->select('address_zip')->orderBy('address_zip')->pluck('address_zip');

        return $zips;
    }

    public function getInput()
    {
        return [

        // BASICS
        'include_deceased' => false,
        'include_archived' => false,

        // LOCATION
        'congressional_districts' => [],
        'senate_districts' => [],
        'house_districts' => [],
        'municipalities' => [],
        'municipalities_narrow' => [],
        'wards' => [],
        'streets' => [],
        'new_streets' => [],
        'zipcodes' => [],

        // DEMOGRAPHICS
        'age_operator' => null,
        'age' => null,
        'gender' => null,
        'parties' => [],
        'weeks_registered' => null,
        'registered_from' => null,

        // VOTING
        'reliability' => ['state' => null, 'local' => null],
        'frequency' => ['state' => ['times' => '', 'year' => ''],
                  'local' => ['times' => '', 'year' => ''], ],
        'primary_ballot' => ['year' => null, 'party' => null],
        'elections' => [],
        'flexqueries' => [],

        // OFFICE
        'linked' => 'no',
        'master_email' => 'no',
        'groups' => [],
        'groups_position' => [],
        'categories' => [],

        // CAMPAIGN
        'support' => [],
        'tags' => [],
        'all_tags' => [],
        'imports' => [],    // kept for backwards compatibility
        'within_lists_any' => [],
        'within_lists_all' => [],
        'subtract_lists' => [],
        'add_lists' => [],
        'any_actions' => [],
        'recent_activity' => null,
        'recent_activity_include' => true,
        'recent_activity_actions' => [],
        'include_volunteers' => [],
        'exclude_volunteers' => [],

        // IMPORTS
        'full_imports' => [],

        // CF PLUS
        'cf_plus' => [
            'cell_phones' => false,
            'ethnicities' => [],
        ],
      ];
    }

    /*
        |===========================================|
        |			Where the Magic Happens  		|
        |===========================================|
    */

    public function buildMainQuery($input = null)
    {
        
        if ($input) {
            $this->input = $input;
        }
        //dd($this->input);
        $main_query = Voter::withTrashed();

        $valid_fields = $this->getInput();
        foreach ($valid_fields as $valid_field => $fieldvals) {
            if (! isset($this->input[$valid_field])) {
                $tempinput = $this->input;
                $tempinput[$valid_field] = null;
                $this->input = $tempinput;
            }
        }
        //dd($this);
        //dd($this->input);

        if (! defined('ELECTION_PROFILES')) {
            define('ELECTION_PROFILES', session('team_state').'_election_profiles');
        }
        if (! defined('ELECTION_RANGES')) {
            define('ELECTION_RANGES', session('team_state').'_election_ranges');
        }

        $num_selected = 0;

         // ===========================================> 0. BASICS + REMOVALS

        $deceased = collect([]);
        $go_away  = Participant::thisTeam()->where('go_away', true)->get();

        if (!$this->input['include_deceased']) {
            $deceased = Participant::thisTeam()->where('deceased', true)->get();
        }

        if (!$this->input['include_archived']) {
            $main_query->whereNull('archived_at');
        }
        //dd("Laz");

        $excluded   = $go_away->merge($deceased);
        $main_query->whereNotIn(session('team_table').'.id', $excluded->pluck('voter_id'));

        // ===========================================> 1. LOCATION
        if ($this->input['congressional_districts']) {
            $num_selected++;
            $district_ids = $this->input['congressional_districts'];
            $f_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'F')->pluck('code');
            if ($f_districts->first()) {
                $main_query->whereIn('congress_district', $f_districts);
            }
        }
        if ($this->input['senate_districts']) {
            $num_selected++;
            $district_ids = $this->input['senate_districts'];
            $s_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'S')->pluck('code');
            if ($s_districts->first()) {
                if ($s_districts->count() == 1) {
                    $main_query->where('senate_district', $s_districts[0]);
                } else {
                    $main_query->whereIn('senate_district', $s_districts);
                }
                
            }
        }
        if ($this->input['house_districts']) {
            $num_selected++;
            $district_ids = $this->input['house_districts'];
            $h_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'H')->pluck('code');
            if ($h_districts->first()) {
                if ($h_districts->count() == 1) {
                    $main_query->where('house_district', $h_districts[0]);
                } else {
                    $main_query->whereIn('house_district', $h_districts);
                }
            }
        }

        // ==========================================> GROUP CITIES / WARDS / STREETS IN QUERY
        //dd("Laz");
        if ($this->input['municipalities']) {
            $num_selected += count($this->input['municipalities']);

            if (count($this->input['municipalities']) > 0) {
                $main_query->where(function ($municipal_narrow_query) {
                    $whole_cities = [];
                    foreach ($this->input['municipalities'] as $mid) {
                        if (isset($this->input['municipalities_narrow'][$mid])) {
                            if ($this->input['municipalities_narrow'][$mid] != 'narrow') {
                                $whole_cities[] = $mid;
                            }
                        } else {
                            $whole_cities[] = $mid;
                        }
                    }
                    if (count($whole_cities) == 1) {
                        $municipal_narrow_query->where('city_code', $whole_cities[0]);
                    } else {
                        $municipal_narrow_query->orWhereIn('city_code', $whole_cities);
                    }

                    foreach ($this->input['municipalities'] as $mid) {
                        if (isset($this->input['municipalities_narrow'][$mid])) {
                            if ($this->input['municipalities_narrow'][$mid] == 'narrow') {
                                $municipal_narrow_query->orWhere(function ($narrow_subquery) use ($mid) {
                                    if ($this->input['wards']) {
                                        $narrow_subquery->where(function ($subquery) use ($mid) {
                                            foreach ($this->input['wards'] as $city_ward_precinct => $on) {
                                                //dd($this->input['wards);

                                                $arr = explode('_', $city_ward_precinct);
                                                $city = $arr[0];
                                                $ward = $arr[1];
                                                $precinct = $arr[2];

                                                if ($city != $mid) {
                                                    continue;
                                                }
                                                if (!$precinct) {
                                                    continue;
                                                }

                                                if ($on) {
                                                    //dd($this->ward_selected_cities);

                                                    $subquery->orWhere(function ($subsubquery) use ($city, $ward, $precinct) {
                                                        $subsubquery->where('city_code', $city)
                                          ->where('precinct', $precinct);
                                                        if ($ward > 0) {
                                                            $subsubquery->where('ward', $ward);
                                                        } else {
                                                            $subsubquery->where(function($sssq) {
                                                                $sssq->where('ward', '<', 1)
                                                                     ->orWhereNull('ward');
                                                            });
                                                        }

                                                        return $subsubquery;
                                                    });
                                                }
                                            }
                                        });
                                    }
                                    if ($this->input['streets']) {

                                        if (isset($this->input['streets'][$mid])) {

                                            $narrow_subquery->where(function ($subquery) use ($mid) {
                                                foreach ($this->input['streets'][$mid] as $street_slug => $street_arr) {
                                                    $subquery->orWhere(function ($subsubquery) use ($mid, $street_arr) {

                                                        //dd($this->input['streets']);
                                                        $subsubquery->where('city_code', $mid)
                                          ->where('address_street', $street_arr['name']);
                                                        if ($street_arr['from']) {
                                                            //dd($this->input['streets']);
                                                            $subsubquery->whereRaw('CAST(address_number as unsigned) >= '.$street_arr['from']);
                                                        }
                                                        if ($street_arr['to']) {
                                                            $subsubquery->whereRaw('CAST(address_number as unsigned) <= '.$street_arr['to']);
                                                        }

                                                        return $subsubquery;
                                                    });
                                                }
                                            });
                                        }
                                    }
                                });
                            }
                        }
                    }
                });
            }
        }

        // ===========================================> ZIP

        if ($this->input['zipcodes']) {
            $num_selected += count($this->input['zipcodes']);

            if (count($this->input['zipcodes']) > 0) {
                $main_query->where(function ($zipcodes_narrow_query) {
                    foreach ($this->input['zipcodes'] as $zip) {
                        $zipcodes_narrow_query->orWhere('address_zip', $zip);
                    }

                    return $zipcodes_narrow_query;
                });
            }
        }

        // ===========================================> 2. DEMOGRAPHICS

        // ====================================================> AGE
        if (isset($this->input['age_operator'])) {
            $age_operator = $this->input['age_operator'];

            if ($age_operator == 'UNKNOWN') {
                $main_query->whereNull('dob');
            } else {
                if (isset($this->input['age'])) {
                    $num_selected++;
                    $age = preg_replace('/[^\d-]+/', '', $this->input['age']);
                    if ($age) {
                        if ($age_operator == '=') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $end_date = Carbon::today()->subYears($age);
                            $main_query->where('dob', '>', $start_date);
                            $main_query->where('dob', '<', $end_date);
                        } elseif ($age_operator == '>') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $main_query->where('dob', '<', $start_date);
                        } elseif ($age_operator == '<') {
                            $start_date = Carbon::today()->subYears($age);
                            $main_query->where('dob', '>', $start_date);
                        } elseif ($age_operator == 'RANGE') {
                            $age_arr = explode('-', $age);
                            $age_from = $age_arr[0];

                            if (isset($age_arr[1])) {
                                $age_to = $age_arr[1];
                                $start_date = Carbon::tomorrow()->subYears($age_to + 1);
                                $end_date = Carbon::yesterday()->subYears($age_from);
                                $main_query->where('dob', '>', $start_date);
                                $main_query->where('dob', '<', $end_date);
                            } else {
                                $start_date = Carbon::today()->subYears($age_from);
                                $main_query->where('dob', '<', $start_date);
                            }
                        }
                    }
                }
            }
        }

        if (isset($this->input['parties'])) {
            $parties = collect($this->input['parties']);
            if ($parties->count() > 0) {
                $num_selected++;
                foreach ($parties as $party) {
                    if ($party == 'Other') {
                        $parties->merge(['B', 'C', 'I']);
                    }
                }
                $main_query->whereIn('party', $parties);
            }
        }

        if (isset($this->input['gender'])) {
            $gender = $this->input['gender'];
            if ($gender) {
                $num_selected++;
                if ($gender == 'BLANK') {
                    $main_query->whereNull('gender');
                } else {
                    $main_query->where('gender', $gender);
                }
            }
        }

        //dd("Laz");

        // ===========================================> 3. VOTING HISTORY
        //dd("Laz2");
        //dd($this->input);

        // (?:20)(?:04|05|06|07)(.*?)(-L0000)

        if (isset($this->input['flexqueries']) && $this->input['flexqueries']) {

            $main_query->select(session('team_table').'.*')
                       ->join(config('database.connections.voters.database').'.'.ELECTION_PROFILES, session('team_table').'.id', '=', ELECTION_PROFILES.'.voter_id');

            foreach($this->input['flexqueries'] as $key => $subquery) {

                if (!isset($subquery['type']) || !$subquery['type']
                    || !isset($subquery['num']) || !$subquery['num']
                    || !isset($subquery['year']) || !$subquery['year']
                ) {
                    continue;
                }

                $num_selected++;

                // select * from MA_election_profiles where year_count LIKE "%2010:2021-L0000>4%"

                $look_for = $subquery['year'].':2021-'.$subquery['type'].'>'.$subquery['num'];

                $main_query = $main_query->where('year_count', 'like', '%'.$look_for.'%');
            }

        }


        if (isset($this->input['weeks_registered']) && $this->input['weeks_registered']) {
            $main_query->where('registration_date', '>', Carbon::today()->subWeeks($this->input['weeks_registered'])->toDateString());
        }

        if (isset($this->input['registered_from']) && $this->input['registered_from']) {
            $main_query->where('registration_date', '>=', Carbon::parse($this->input['registered_from'])->toDateString());
        }

        if (isset($this->input['reliability']['local'])
          || isset($this->input['reliability']['state'])
          || isset($this->input['primary_ballot']['year'])) {

            $main_query->join(config('database.connections.voters.database').'.'.ELECTION_PROFILES, session('team_table').'.id', '=', ELECTION_PROFILES.'.voter_id');

            if ($this->input['reliability']['local'] || $this->input['reliability']['state']) {
            
                $num_selected++;
                $main_query->selectRaw(session('team_table').'.*, '.ELECTION_PROFILES.'.stalwart_local, '.ELECTION_PROFILES.'.stalwart_state, '.ELECTION_PROFILES.'.reliable_local, '.ELECTION_PROFILES.'.reliable_state, '.ELECTION_PROFILES.'.somewhat_local, '.ELECTION_PROFILES.'.somewhat_state, '.ELECTION_PROFILES.'.primary_ballot_2016, '.ELECTION_PROFILES.'.primary_ballot_2018, '.ELECTION_PROFILES.'.primary_ballot_2020');

                

                if ($this->input['reliability']['local']) {
                    $local_column = $this->input['reliability']['local'].'_local';
                    $main_query->where(ELECTION_PROFILES.'.'.$local_column, true);
                }
                if ($this->input['reliability']['state']) {
                    $state_column = $this->input['reliability']['state'].'_state';
                    $main_query->where(ELECTION_PROFILES.'.'.$state_column, true);
                }

            }
            if (isset($this->input['primary_ballot']['year'])) {
                if ($this->input['primary_ballot']['year']) {
                    $primary_year = $this->input['primary_ballot']['year'];
                    $primary_party = $this->input['primary_ballot']['party'];

                    if ($primary_party == 'D' || $primary_party == 'R') {
                        $main_query->where(ELECTION_PROFILES.'.primary_ballot_'.$primary_year, $primary_party);
                    } else {
                        $main_query->whereNotNull(ELECTION_PROFILES.'.primary_ballot_'.$primary_year);
                    }
                }
            }
            
            //dd($main_query->toSql());
        //dd($main_query->toSql());
        }


        if (isset($this->input['frequency']['local']['times']) && isset($this->input['frequency']['state']['times'])) {
            if ($this->input['frequency']['local']['times'] || $this->input['frequency']['state']['times']) {
                $num_selected++;
                // $main_query->selectRaw(session('team_table').'.*, election_ranges.stalwart_local, election_profiles.stalwart_state, election_profiles.reliable_local, election_profiles.reliable_state, election_profiles.somewhat_local, election_profiles.somewhat_state');

                $main_query->join(config('database.connections.voters.database').'.'.ELECTION_RANGES, session('team_table').'.id', '=', ELECTION_RANGES.'.voter_id');

                if ($this->input['frequency']['local']['times']) {
                    $local_times = $this->input['frequency']['local']['times'];
                    $local_column = '1220_local_any';
                    if ($this->input['frequency']['local']['year']) {
                        $full_year = $this->input['frequency']['local']['year'];
                        $local_year = substr($full_year, 2, 2).'20';
                        //dd($local_year, $full_year);
                        $local_column = $local_year.'_local_any';
                    }
                    $main_query->where(ELECTION_RANGES.'.'.$local_column, '>=', $local_times);
                }

                if ($this->input['frequency']['state']['times']) {
                    $state_times = $this->input['frequency']['state']['times'];
                    $state_column = '1220_state_any';
                    if ($this->input['frequency']['state']['year']) {
                        $full_year = $this->input['frequency']['state']['year'];
                        $state_year = substr($full_year, 2, 2).'20';
                        //dd($state_year, $full_year);
                        $state_column = $state_year.'_state_any';
                    }
                    $main_query->where(ELECTION_RANGES.'.'.$state_column, '>=', $state_times);
                }
                //dd($main_query->toSql());
          //dd($main_query->toSql());
            }
        }

        if (isset($this->input['primary_ballot']['year'])) {

        //dd($primary_year, $primary_party, $main_query->toSql());
        }

        // OLDEN SYSTEM:

        // if (isset($this->input['elections'])) {
            
        //     if (is_array($this->input['elections'])) {

        //         if (count($this->input['elections']) > 0) {
        //             $num_selected++;

        //             foreach ($this->input['elections'] as $election_id) {
        //                 $main_query->where('elections', 'LIKE', '%'.$election_id.'%');
        //             }
        //         }
                
        //     }
        // }


        if (isset($this->input['elections']) && is_array($this->input['elections']) && count($this->input['elections']) > 0) {

            if(!isset($this->input['electionsCount'])) { 
                $tempinput = $this->input;
                $tempinput['electionsCount'] = null;
                $this->input = $tempinput;
            }

            $num_selected++;

            //-------- ALL SELECTED

            if (!$this->input['electionsCount']
                || $this->input['electionsCount'] == count($this->input['elections'])) {

                foreach ($this->input['elections'] as $election_id) {
                    $main_query->where('elections', 'LIKE', '%'.$election_id.'%');
                }

            }

            //-------- SOME SELECTED

            if ($this->input['electionsCount']
                && $this->input['electionsCount'] < count($this->input['elections'])) {

                $if_clause = [];

                foreach ($this->input['elections'] as $election_id) {
                    $if_clause[] = 'IF(elections LIKE "%'.$election_id.'%", 1, 0)';
                }

                $if_clause = implode(' + ', $if_clause);
                $main_query->whereRaw('('.$if_clause.' >= '.$this->input['electionsCount'].')');

            }
                
        }


        // ===========================================> 4. OFFICE

        if (isset($this->input['linked'])) {
            if ($this->input['linked'] == 'yes') {
                $num_selected += 1;
                $office_team = Auth::user()->office_team;
                $voter_ids = $office_team->people()
                                   ->whereNotNull('voter_id')
                                   ->pluck('voter_id');
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
            }
        }

        if (isset($this->input['master_email'])) {
            if ($this->input['master_email'] == 'yes') {
                $num_selected += 1;
                $office_team = Auth::user()->office_team;
                $voter_ids = $office_team->people()
                                   ->whereNotNull('voter_id')
                                   ->where('master_email_list', true)
                                   ->pluck('voter_id');
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
            }
        }

        if (isset($this->input['groups'])) {
            if (count($this->input['groups']) > 0) {
                $office_team = Auth::user()->office_team;
                if ($office_team) {
                    $num_selected += count($this->input['groups']);
                    $main_query->where(function ($group_positions_query) use ($office_team) {
                        $whole_groups = [];
                        foreach ($this->input['groups'] as $group) {
                            if (is_array($group)) {
                                $gid = $group['id'];
                            } else {
                                $gid = $group->id;
                            }

                            if (isset($this->input['groups_position'][$gid])) {
                                if ($this->input['groups_position'][$gid] == 'all') {
                                    $whole_groups[] = $gid;
                                }
                            } else {
                                $whole_groups[] = $gid;
                            }
                        }
                        //dd($whole_groups);
                        $person_ids = GroupPerson::whereIn('group_id', $whole_groups)->pluck('person_id');
                        $voter_ids = $office_team->people()
                                           ->whereIn('id', $person_ids)
                                           ->whereNotNull('voter_id')
                                           ->pluck('voter_id');

                        //dd($voter_ids);
                        $group_positions_query->orWhereIn(session('team_table').'.id', $voter_ids);

                        foreach ($this->input['groups'] as $group) {
                            if (is_array($group)) {
                                $gid = $group['id'];
                            } else {
                                $gid = $group->id;
                            }
                            if (isset($this->input['groups_position'][$gid])) {
                                $position = $this->input['groups_position'][$gid];
                                $person_ids = GroupPerson::where('group_id', $gid)
                                               ->where('position', $position)
                                               ->pluck('person_id');

                                $voter_ids = $office_team->people()
                                               ->whereIn('id', $person_ids)
                                               ->whereNotNull('voter_id')
                                               ->pluck('voter_id');

                                //dd($voter_ids);
                                $group_positions_query->orWhereIn(session('team_table').'.id', $voter_ids);
                            }
                        }
                    });
                }
            }
        }

        // ===========================================> 5. CAMPAIGN DATA

        if (isset($this->input['any_actions']) && count($this->input['any_actions']) > 0) {

            $num_selected += count($this->input['any_actions']);

            $voter_ids = collect([]);

            foreach ($this->input['any_actions'] as $action) {
                $voter_ids = $voter_ids->merge(
                                Action::thisTeam()->whereIn('name', $this->input['any_actions'])->pluck('voter_id')
                            );
            }
            
            $main_query->whereIn(session('team_table').'.id', $voter_ids->unique());
        }

        if (isset($this->input['recent_activity'])) {
            $num_selected += 1;
            $daysback = $this->input['recent_activity'];
            //dd($daysback);
            $recent_actions_query = Action::thisTeam()->where('created_at', '>=', Carbon::today()->subDays($daysback));

            if (isset($this->input['recent_activity_actions']) 
                        && count($this->input['recent_activity_actions']) > 0) {
                $recent_actions_query->whereIn('name', $this->input['recent_activity_actions']);
            }
            $recent_action_voter_ids = $recent_actions_query->pluck('voter_id')
                                                            ->unique();

            if ($this->input['recent_activity_include']) {
                $main_query->whereIn(session('team_table').'.id', $recent_action_voter_ids);
            } else {
                $main_query->whereNotIn(session('team_table').'.id', $recent_action_voter_ids);
            }
        }

        if (isset($this->input['support'])) {
            if (count($this->input['support']) > 0) {
                $num_selected += count($this->input['support']);
                $campaign = CurrentCampaign();
                $voter_ids = CampaignParticipant::where('campaign_id', $campaign->id)
                                          ->whereIn('support', $this->input['support'])
                                          ->pluck('voter_id');
                //dd($voter_ids);
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
            }
        }

        if (isset($this->input['tags'])) {
            if (count($this->input['tags']) > 0) {
                $num_selected += count($this->input['tags']);
                $voter_ids = ParticipantTag::whereIn('tag_id', $this->input['tags'])->pluck('voter_id');
                //dd($voter_ids);
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
            }
        }

        if (isset($this->input['all_tags'])) {
            if (count($this->input['all_tags']) > 0) {
                $num_selected += count($this->input['all_tags']);

                foreach ($this->input['all_tags'] as $tag_id) {
                    $voter_ids = ParticipantTag::where('tag_id', $tag_id)->pluck('voter_id');
                    $main_query->whereIn(session('team_table').'.id', $voter_ids);
                }
                //dd($voter_ids);
                
            }
        }

        if (isset($this->input['volunteers'])) {
            if ($this->input['volunteers']) {

                $num_selected += 1;
                if (isset($this->input['volunteers_specific'])) {

                    $num_selected += count($this->input['volunteers_specific']);
                    $voter_ids = Participant::volunteers($this->input['volunteers_specific'])->pluck('voter_id');
                } else {
                    $voter_ids = Participant::volunteers()->pluck('voter_id');
                }

                if (request('volunteer_debug')) {
                    dd($voter_ids);
                }
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
            }
        }

        if (isset($this->input['imports'])) {
            if (count($this->input['imports']) > 0) {
                $num_selected += count($this->input['imports']);
                $voter_ids = UserUploadData::where('team_id', Auth::user()->team->id)
                                     ->whereIn('upload_id', $this->input['imports'])
                                     ->whereNotNull('voter_id')
                                     ->pluck('voter_id');
                //dd($voter_ids);
                $main_query->whereIn(session('team_table').'.id', $voter_ids);
                //dd($main_query->toSql());

            }
        }

        if (isset($this->input['full_imports'])) {
            if (count($this->input['full_imports']) > 0) {
                $num_selected += count($this->input['full_imports']);

                $voter_ids = [];

                foreach ($this->input['full_imports'] as $import_id => $import_arr) {

                    $import = UserUpload::find($import_id);
                    
                    

                    //dd($key_lookup);
                    
                    if (isset($import_arr['use']) && $import_arr['use']) {
                        //dd($import_id);

                        if (isset($import_arr['filters']) && count($import_arr['filters']) > 0) {

                            if ($import->columns) {
                                $key_lookup = array_flip($import->columns);
                            }
                            $filtered = false;
                            foreach ($import_arr['filters'] as $fil_name => $fil_val) {
                                if ($fil_name && $fil_val) {
                                    $filtered = true;
                                }
                            }

                            if (!$filtered) {

                                $import_ids = UserUploadData::where('team_id', Auth::user()->team->id)
                                                     ->where('upload_id', $import_id)
                                                     ->whereNotNull('voter_id')
                                                     ->pluck('voter_id');
                                foreach ($import_ids as $id) {
                                    $voter_ids[$id] = 1;
                                }

                            } else {

                                $import_data = UserUploadData::where('team_id', Auth::user()->team->id)
                                                     ->where('upload_id', $import_id)
                                                     ->whereNotNull('voter_id')
                                                     ->get();

                                //dd($import_arr['filters']);
                                foreach ($import_data as $onedata) {
                                    foreach ($import_arr['filters'] as $fil_name => $fil_val) {
                                        if ($fil_name && $fil_val) {
                                            //dd($key_lookup, $fil_name, $fil_val, $onedata->data);
                                            $filterkey = $key_lookup[$fil_name];
                                            if (isset($onedata->data[$filterkey])) {
                                                if ($onedata->data[$filterkey] == $fil_val) {
                                                    $voter_ids[$onedata->voter_id] = 1;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $import_ids = UserUploadData::where('team_id', Auth::user()->team->id)
                                                 ->where('upload_id', $import_id)
                                                 ->whereNotNull('voter_id')
                                                 ->pluck('voter_id');
                            foreach ($import_ids as $id) {
                                $voter_ids[$id] = 1;
                            }

                        }
                        //dd($import_ids);
                        
                    }
                    //dd($main_query->toSql());
                }
                //dd($voter_ids);

                $main_query->whereIn(session('team_table').'.id', array_keys($voter_ids));

            }
        }

        if (isset($this->input['within_lists_all'])) {
            if (count($this->input['within_lists_all']) > 0) {
                $num_selected += count($this->input['within_lists_all']);
                
                foreach ($this->input['within_lists_all'] as $slid) {
                    $sl = CampaignList::find($slid);
                    //dd($sl->form);
                    if ($sl) {
                        $sl_query = $sl->voters();
                        $within_ids = $sl_query->pluck(session('team_table').'.id');
                        $main_query->whereIn(session('team_table').'.id', $within_ids);
                    }
                } 
            }
        }

        if (isset($this->input['within_lists_any'])) {
            if (count($this->input['within_lists_any']) > 0) {
                $num_selected += count($this->input['within_lists_any']);
                
                $within_ids = collect([]);
                foreach ($this->input['within_lists_any'] as $slid) {
                    $sl = CampaignList::find($slid);
                    //dd($sl->form);
                    if ($sl) {
                        $sl_query = $sl->voters();
                        $within_ids = $within_ids->merge($sl_query->pluck(session('team_table').'.id'));
                    }
                    
                } 
                $main_query->whereIn(session('team_table').'.id', $within_ids);
            }
        }

        if (isset($this->input['exclude_volunteers'])
            && !empty($this->input['exclude_volunteers'])) {

            $campaign = CurrentCampaign();

            $voter_ids = CampaignParticipant::where('campaign_id', $campaign->id);

            $rawSql = '('.implode(' + ', $this->input['exclude_volunteers']).' > 1)';
            $voter_ids = $voter_ids->whereRaw($rawSql)->pluck('voter_id');
            
            $main_query->whereNotIn(session('team_table').'.id', $voter_ids);
            
        }
        
        if (isset($this->input['include_volunteers'])
            && !empty($this->input['include_volunteers'])) {

            $campaign = CurrentCampaign();

            $voter_ids = CampaignParticipant::where('campaign_id', $campaign->id);

            $rawSql = '('.implode(' + ', $this->input['include_volunteers']).' > 1)';
            $voter_ids = $voter_ids->whereRaw($rawSql)->pluck('voter_id');
            
            $main_query->whereIn(session('team_table').'.id', $voter_ids);
            
        }
        
        

        // ===========================================> 6. CF+

        if (isset($this->input['cf_plus']['cell_phones'])) {
            if ($this->input['cf_plus']['cell_phones']) {
                //dd("Laz");
                $num_selected += 1;

                $cf_plus_table = config('database.connections.voters.database').'.CF_PLUS_FULL as cf1';

                $main_query->join($cf_plus_table, session('team_table').'.id', '=', 'cf1.voter_id')
                    ->whereNotNull('cf1.cell_phone');

            }
        }

        if (isset($this->input['cf_plus']['ethnicities'])) {
            if ($this->input['cf_plus']['ethnicities']) {
                //dd("Laz");
                $num_selected += 1;

                $cf_plus_table = config('database.connections.voters.database').'.CF_PLUS_FULL as cf2';

                $main_query->join($cf_plus_table, session('team_table').'.id', '=', 'cf2.voter_id')
                    ->whereIn('cf2.ethnic_description', $this->input['cf_plus']['ethnicities']);

            }
        }

        if (isset($this->input['add_lists'])) {
            if (count($this->input['add_lists']) > 0) {
                //dd($this->input['add_lists']);
                $all_ids_to_add = collect([]);
                foreach ($this->input['add_lists'] as $slid) {
                    $sl = CampaignList::find($slid);
                    //dd($sl->form);
                    if ($sl) {
                        $sl_query = $sl->voters();
                        $add_ids = $sl_query->pluck(session('team_table').'.id');
                        //dd($add_ids);
                        $all_ids_to_add = $all_ids_to_add->merge($add_ids);
                        $all_ids_to_add->unique();
                    }

                } 
                //dd($all_ids_to_add);
                if ($num_selected > 0) {
                    $main_query->orWhereIn(session('team_table').'.id', $all_ids_to_add);
                } else {
                    $main_query->whereIn(session('team_table').'.id', $all_ids_to_add);
                }

                $num_selected += count($this->input['add_lists']);
            }
        }

        if (isset($this->input['subtract_lists'])) {
            if (count($this->input['subtract_lists']) > 0) {
                $num_selected += count($this->input['subtract_lists']);
                
                foreach ($this->input['subtract_lists'] as $slid) {
                    $sl = CampaignList::find($slid);
                    //dd($sl->form);
                    if ($sl) {
                        $sl_query = $sl->voters();
                        $sub_ids = $sl_query->pluck(session('team_table').'.id');
                        $main_query->whereNotIn(session('team_table').'.id', $sub_ids);
                    }
                } 
            }
        }

        // ===========================================> CALCULATE

        $this->num_selected = $num_selected;
        //dd($num_selected);
        if ($this->num_selected > 0) {
            $this->current_count = $main_query->count();
        } else {
            $this->current_count = 0;
        }
        $this->debug = $main_query->toSql();
        //dd($this->debug);

        // dd($main_query, $main_query->toSql(), $this->current_count);
        // dd($main_query->get());
        return $main_query;
    }
}
