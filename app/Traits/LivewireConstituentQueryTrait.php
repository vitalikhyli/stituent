<?php

namespace App\Traits;
use Auth;
use App\Person;
use App\Voter;
use App\Municipality;
use App\BulkEmailQueue;
use App\BulkEmail;
use App\GroupPerson;
use App\Category;
use App\District;

use Carbon\Carbon;

use DB;

use Log;


trait LivewireConstituentQueryTrait
{

    public function getPeopleIDsForEachGroup($groups) 
    {
        $people_pivots = [];

        foreach($groups as $group_id => $options) {

            foreach($options as $support_type => $data) {

                if ($support_type == 'main') {

                    $person_ids = GroupPerson::where('group_id',$group_id)
                                        ->where('team_id',Auth::user()->team->id)
                                        
                                        ->pluck('person_id')
                                        ->toArray();

                } else {

                    if ($support_type == 'support') $support_type = 'supports';
                    if ($support_type == 'oppose') $support_type = 'opposed';

                    $person_ids = GroupPerson::where('group_id',$group_id)
                                        ->where('team_id',Auth::user()->team->id)
                                        
                                        ->where('position', $support_type)
                                        ->pluck('person_id')
                                        ->toArray();

                }

                $people_pivots = array_merge($people_pivots, $person_ids);

            }
        }

        return Person::where('team_id', Auth::user()->team->id)
                     ->whereIn('id', $people_pivots)
                     ->pluck('id')
                     ->unique();
    }


    public function getMunicipalities($lookup = null)
    {

        //dd("Laz");
        // Done like this to use archived_at, city_code index
        $city_codes =  Voter::withTrashed()
                            ->whereNull('archived_at')
                            ->groupBy('city_code')
                            ->pluck('city_code');
        // $city_codes =  Voter::withTrashed()->whereNull('archived_at')->distinct()->pluck('city_code');
        //dd($city_codes);
        $municipalities = Municipality::where('state', session('team_state'))
                                      ->whereIn('code', $city_codes);

        if ($lookup) {
            $municipalities = $municipalities->where('name', 'like', '%'.$lookup.'%');
        }

        $municipalities = $municipalities->orderBy('name')->get();

        return $municipalities;
    }


    public function getDistricts()
    {
        $types = ['F' => 'congress_district',
                  'H' => 'house_district',
                  'S' => 'senate_district'];

        $districts = collect([]);

        foreach($types as $code => $column_name) {

            $district_ids = Voter::withTrashed()
                                 ->distinct()
                                 ->select($column_name)
                                 ->pluck($column_name)
                                 ->unique();

            $add_districts = District::where('type', $code)
                                     ->where('state', session('team_state'))
                                     ->whereIn('code', $district_ids)
                                     ->orderBy('sort')
                                     ->get();

            $districts = $districts->merge($add_districts);

        }
        
        return $districts;
    }

    public function getZips($lookup = null)
    {
        $zips =  Voter::withTrashed()->distinct()
                                     ->select('address_zip')
                                     ->orderBy('address_zip')
                                     ->pluck('address_zip');

        if ($lookup) {
            $zips = $zips->filter(function ($item) use ($lookup) {
                return false !== strpos($item, $lookup);
            });
        }

        return $zips;
    }


    public function constituentQuery($input, $limit=null, $fields=null, $start_from_zero=null)
    {
        //dd($input);
        $no_input_given = false;

        if (!$input['search_value']
            && !$input['street']
            && !$input['age']
            && !$input['email']
            && empty($input['selected_groups'])
            && empty($input['selected_districts'])
            && empty($input['selected_cities'])
            && empty($input['precincts'])
            && empty($input['selected_zips'])
            && empty($input['parties'])
            && !$input['ignore_archived']
            && !$input['ignore_deceased']
           ) {

            $no_input_given = true;
        }

        // ====================================================> SET UP
        if ($limit == 'none') {
            $limit = null;
        } elseif (!$limit) {
            $limit = ($input['per_page']) ? $input['per_page'] : 100; // Defaults to 100
        } 

        $start = microtime(-1);
        $this->time = 0;

        if (Person::first()) $people_keys = array_keys(Person::first()->toArray());
        if (Voter::first())  $voter_keys  = array_keys(Voter::first()->toArray());

        // ====================================================> START FROM ZERO

        if ($start_from_zero) {

            $people = Person::where('id', 'THIS_ID_DOES_NOT_EXIST');
            $voters = Voter::where('id', 'THIS_ID_DOES_NOT_EXIST');
                   
        }

        // ====================================================> START FROM ALL (+SELECT FIELDS)

        if (!isset($people) || !isset($voters)) {

            if (!$fields) {

                $people = Person::where('team_id', Auth::user()->team->id);
                $voters = Voter::withTrashed();

            }

            if ($fields) {

                $people = Person::where('team_id', Auth::user()->team->id);
                $voters = Voter::withTrashed();

                $people_fields  = $fields;
                if (!in_array('id', $people_fields))        { $people_fields[] = 'id'; }
                if (!in_array('voter_id', $people_fields))  { $people_fields[] = 'voter_id'; }

                $voter_fields   = $fields;
                if (!in_array('id', $voter_fields))         { $voter_fields[] = 'id'; }

            }

        }

        // ====================================================> MASTER EMAIL

        if ($input['master_email']) {
            $people->where('master_email_list', true);
            $input['linked'] = true; // Only People No Voter
        }

        if ($input['voter_has_email']) {
            //dd("Laz");
            $voters->where('emails', '!=', '[]');
        }


        // ====================================================> GROUPS


        if ($input['selected_groups'] && !empty($input['selected_groups'])) {
            $input['linked'] = true;
            $people_collection = $this->getPeopleIDsForEachGroup($input['selected_groups']);
            $people->whereIn('id', $people_collection);
        }


        // ====================================================> VOTERS LINKED/IGNORED

        if ($input['linked']) {
            $voters->where('id', 'THIS_ID_DOES_NOT_EXIST');
        }

        if ($input['ignore_archived']) {
            $voters->whereNull('archived_at');
        }

        if ($input['ignore_deceased']) {

            // $deceased_ids = Person::where('team_id', Auth::user()->team->id)
            //                       ->where('deceased', true)
            //                       ->pluck('id');

            // $people->whereNotIn('id', $deceased_ids); // Boolean acts strangely because nullable?

            // $deceased_voter_ids = Person::where('team_id', Auth::user()->team->id)
            //                             ->where('deceased', true)
            //                             ->whereNotNull('voter_id')
            //                             ->pluck('voter_id');

            // $voters = $voters->whereNotIn('id', $deceased_voter_ids);
            // $voters = $voters->where('deceased', false);
            //// $voters = $voters->whereNull('deceased_date');
        }

        // ====================================================> FIRST NAME
        if ($input['first_name']) {
            $first_name = str_replace('*', '%', $input['first_name']);
            $voters->where('first_name', 'LIKE', $first_name."%");
            $people->where('first_name', 'LIKE', $first_name."%");
        }
        // ====================================================> LAST NAME
        if ($input['last_name']) {
            $last_name = str_replace('*', '%', $input['last_name']);
            $voters->where('last_name', 'LIKE', $last_name."%");
            $people->where('last_name', 'LIKE', $last_name."%");

        }

        // ====================================================> MIDDLE NAME
        if (isset($input['last_name']) && $input['middle_name']) {
                $middle_name = $input['middle_name'];
                $voters->where('middle_name', 'LIKE', $middle_name."%");
                $people->where('middle_name', 'LIKE', $middle_name."%");
        }

        // ====================================================> EMAIL

        if ($input['email']) {

            $input['email'] = preg_replace("/".'[^a-zA-Z0-9@._-]'."/", '', $input['email']);

            $personal_emails = Person::where('team_id', Auth::user()->team->id)
                             ->where('primary_email', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $work_emails = Person::where('team_id', Auth::user()->team->id)
                             ->where('work_email', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $other_emails = Person::where('team_id', Auth::user()->team->id)
                             ->where('other_emails', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $emails = $personal_emails->merge($work_emails)->merge($other_emails);

            $people->whereIn('id', $emails->pluck('id')->toArray());
            $voters = Voter::where('id', 'THIS_ID_DOES_NOT_EXIST'); // DO not search boters

        }



        // ====================================================> STREET NAME
        if ($input['street']) {
            $street = str_replace('*', '%', $input['street']);
            $street_num = preg_replace('/\D/', '', $street);
            $street_name = trim(preg_replace('/\d/', '', $street));
            //dd($street, $name, $street_num);
            if ($street_num) {
                $voters->where('address_number', $street_num);
                $people->where('address_number', $street_num);
            }
            if ($street_name) {
                $voters->where('address_street', 'LIKE', $street_name."%");
                $people->where('address_street', 'LIKE', $street_name."%");
            }
        }
        


        // ====================================================> MUNICIPALITIES
        if ($input['selected_cities']) {
            $voters->whereIn('city_code', $input['selected_cities']);
            $people->whereIn('city_code', $input['selected_cities']);
        }

        if ($input['precincts']) {
            $split = explode(' ',$input['precincts']);
            $voters->whereIn('precinct', $split);
            $people->whereIn('precinct', $split);
        }

        // ====================================================> DISTRICTS
        if ($input['selected_districts']) {

            foreach(['F' => 'congress',
                     'H' => 'house',
                     'S' => 'senate'] as $district_type => $district_english) {

                $district_ids = District::whereIn('id', $input['selected_districts'])
                                        ->where('type', $district_type)
                                        ->pluck('code');

                if ($district_ids->first()) {
                    $voters->whereIn($district_english.'_district', $district_ids);
                    $people->whereIn($district_english.'_district', $district_ids);
                }

            }
        }


        // ====================================================> ZIP CODES
        if ($input['selected_zips']) {
            $voters->whereIn('address_zip', $input['selected_zips']);
            $people->whereIn('address_zip', $input['selected_zips']);
        }

        // ====================================================> PARTY
        if ($input['parties']) {

            $parties = collect($input['parties']);
            if ($parties) {
                foreach ($parties as $party) {
                    if ($party == 'Other') {
                        $parties->merge(['B','C','I']);
                    }
                }
                $voters->whereIn('party', $parties);
                $people->whereIn('party', $parties);
            }
        }

        

        // ====================================================> AGE
        if ($input['age_operator']) {
            $age_operator = $input['age_operator'];
            
            if ($age_operator == 'UNKNOWN') {
                $voters->whereNull('dob');
                $people->whereNull('dob');
            } else {

                if ($input['age']) {
                    $age = preg_replace('/[^\d-]+/', '', $input['age']);
                    if ($age) {
                        if ($age_operator == '=') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $end_date = Carbon::today()->subYears($age);
                            $voters->where('dob', '>', $start_date);
                            $voters->where('dob', '<', $end_date);
                            $people->where('dob', '>', $start_date);
                            $people->where('dob', '<', $end_date);
                        } else if ($age_operator == '>') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $voters->where('dob', '<', $start_date);
                            $people->where('dob', '<', $start_date);
                        } else if ($age_operator == '<') {
                            $start_date = Carbon::today()->subYears($age);
                            $voters->where('dob', '>', $start_date);
                            $people->where('dob', '>', $start_date);
                        } else if ($age_operator == 'RANGE') {
                            $age_arr = explode('-', $age);
                            $age_from = $age_arr[0];
                            
                            if (isset($age_arr[1])) {
                                $age_to = $age_arr[1];
                                $start_date = Carbon::tomorrow()->subYears($age_to + 1);
                                $end_date = Carbon::yesterday()->subYears($age_from);
                                $voters->where('dob', '>', $start_date);
                                $voters->where('dob', '<', $end_date);
                                $people->where('dob', '>', $start_date);
                                $people->where('dob', '<', $end_date);
                            } else {
                                $start_date = Carbon::today()->subYears($age_from);
                                $voters->where('dob', '<', $start_date);
                                $people->where('dob', '<', $start_date);
                            }

                        }
                    }
                    
                }

                
            }
        }
        // ====================================================> EMAILS
        // if ($input['email_not_null']) {
        //     $people->whereNotNull('primary_email');
        // }

        // ====================================================> HAS RECEIVED EMAILS 

        if ($input['selected_emails']) {
            $emails = collect($input['selected_emails']);
            foreach ($emails as $the_email_id) {
                $recipients_people = BulkEmailQueue::where('bulk_email_id',$the_email_id)
                                                   ->pluck('person_id')
                                                   ->toArray();

                // Not reliable?
                // $recipients_voters = BulkEmailQueue::where('bulk_email_id',$the_email_id)
                //                                    ->whereNotNull('voter_id')
                //                                    ->pluck('voter_id')
                //                                    ->toArray();

                $recipients_voters = [];
                foreach($recipients_people as $person_id) {
                    $person = Person::find($person_id);
                    if ($person) {
                        $voter_id = $person->voter_id;
                        if ($voter_id) $recipients_voters[] = $voter_id;
                    }
                }

                $people->whereNotIn('id', $recipients_people);
                $voters->whereNotIn('id', $recipients_voters);
            }
        }


        // ====================================================> PEOPLE + VOTERS + COUNT


        if ($input['linked']) { // People Only
            
            $this->total_count = $people->count();  
            $this->total_count_people = $this->total_count; 
            $this->total_count_voters = null;

        } elseif ($no_input_given) {    // All Constituents

            $this->total_count = Auth::user()->team->constituents_count;
            $this->total_count_people = $people->count();
            $this->total_count_voters = $this->total_count - $this->total_count_people;

        } else {

            $this->total_count_voters = (clone $voters)->count();
            $this->total_count_people = (clone $people)->count();
            $this->total_count = $this->total_count_people + $this->total_count_voters;

        }
        

        // ====================================================> ORDER BY
        if ($input['order_by']) {

            if ($input['order_by'] == 'dob') {
                // "Age" makes more sense reversed asc/desc
                $input['order_direction'] = ($input['order_direction'] == 'asc') ? 'desc' : 'asc';
            }
            
            if ($input['order_direction'] == 'desc') {
                $voters->orderBy($input['order_by'], 'desc');
                $people->orderBy($input['order_by'], 'desc');
            } else {
                $voters->orderBy($input['order_by']);
                $people->orderBy($input['order_by']);
            }
        }

        // ====================================================> COLLECTIONS
        if (isset($input['return_queries'])) {
            $queries = [];
            $queries['people'] = $people;
            $queries['voters'] = $voters;
            return $queries;
        }
        if (isset($input['all_people'])) {
            $people_collection = $people->get();
        } else {
            if ($limit) $people_collection  = $people->take($limit)->get();
            if (!$limit) $people_collection = $people->get();
        }

        $valid_voterids = [];
        foreach ($people_collection->pluck('voter_id') as $vid) {
            if ($vid) {
                $valid_voterids[] = $vid;
            }
        }
        $voters_collection = $voters->whereNotIn('id', $valid_voterids);


        if ($limit) $voters_collection = $voters_collection->take($limit)->get();
        if (!$limit) $voters_collection = $voters_collection->get();
                 

        // ====================================================> TO DISTINGUISH IN VIEWS

        if (!$fields) { // I.E. THIS IS NOT AN EXPORT

            $people_collection->map(function ($model) {
                $model['person'] = true;
                return $model;
            });

            $voters_collection->map(function ($model) {
                $model['person'] = false;
                return $model;
            });

        }

        // ====================================================> MERGE
        $constituents = $people_collection->merge($voters_collection);        
        if ($limit) $constituents = $constituents->take($limit);
        $constituents = $constituents->sortBy('last_name');


        if (isset($input['order_by'])) {
            if (isset($input['order_direction'])) {
                $constituents = $constituents->sortByDesc($input['order_by']);
            } else {
                $constituents = $constituents->sortBy($input['order_by']);
            }
        }


        // ====================================================> REMOVE ID AND VOTER_ID
        if ($fields) { //IF THIS IS AN EXPORT
            $constituents->transform(function($i) {
                unset($i->id);
                unset($i->voter_id);
                return $i;
            });
        }

        // ====================================================> RETURN

        $this->time = microtime(-1) - $start;
        $this->loaded_times++;


        return $constituents;

    }
}

?>