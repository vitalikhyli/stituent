<?php

namespace App\Traits;

use App\BulkEmail;
use App\BulkEmailQueue;
use App\Category;
use App\District;
use App\GroupPerson;
use App\Municipality;
use App\Person;
use App\WorkCasePerson;
use App\Voter;
use Auth;
use Carbon\Carbon;
use DB;
use Log;

trait ConstituentQueryTrait
{
    public function bulkEmailQuery($input, $mode = null)
    {

        // ====================================================> SET UP

        $people_collection = collect([]);

        // ====================================================> MASTER EMAIL

        if (isset($input['master_email'])) {
            if ($input['master_email'] == 'on') {
                $people_collection = Person::where('team_id', Auth::user()->team->id)
                                           ->where('master_email_list', true)
                                           ->get();
            }
        }

        // ====================================================> REMOVALS

        // Add this?

        // ====================================================> MARKETING

        if (isset($input['sales_type'])) {
            $sales_entities = \App\Models\Business\SalesEntity::where('team_id', Auth::user()->team->id)
                                                              ->where('type', $input['sales_type']);

            if (isset($input['sales_clients_or_prospects'])) {
                if ($input['sales_clients_or_prospects'] == 'clients') {
                    $sales_entities = $sales_entities->where('client', true);
                }

                if ($input['sales_clients_or_prospects'] == 'prospects') {
                    $sales_entities = $sales_entities->where('client', '!=', true);
                }
            }

            $sales_entities = $sales_entities->get();

            foreach ($sales_entities as $sales_entity) {
                $users = $sales_entity->entity->people;
                $people_collection = $people_collection->merge($users);
            }
        }

        // ====================================================> GROUPS
        $categories = $this->getGroupCategoriesFromInput($input);

        //dd($input);

        if ($categories->first()) {

            //print_r($categories->implode('name', ', '));

            $people_collection = $this->processCategoriesAndGroups($categories, $input, $people_collection);
        }

        // ====================================================> EMAILS

        if (isset($input['has_received_emails'])) {
            $emails = collect($input['has_received_emails']);
            foreach ($emails as $the_email_id) {
                $recipients = BulkEmailQueue::where('bulk_email_id', $the_email_id)
                                            ->pluck('person_id');

                $received_collection = Person::where('team_id', Auth::user()->team->id)->whereIn('id', $recipients)->get();
                $people_collection = $people_collection->merge($received_collection);
            }
        }

        // THIS COULD RESULT IN SOMEONE WHO HAS ALREADY RECEIVED EMAIL X GETTING THE EMAIL AGAIN
        // IF THEY GET INTO THE LIST FOR ANOTHE REASONS SUCH AS BELONGING TO A GROUP,
        // OR HAVE NOT RECEIVED EMAIL Y.
        //
        // CONFUSING? AN ARGUMENT FOR HAVING REMOVALS VS ADDITIONS?

        if (isset($input['has_not_received_emails'])) {
            $emails = collect($input['has_not_received_emails']);
            foreach ($emails as $the_email_id) {
                $recipients = BulkEmailQueue::where('bulk_email_id', $the_email_id)
                                            ->pluck('person_id');

                $people_collection = $people_collection->whereNotIn('id', $recipients);
            }
        }

        if (isset($input['case_type'])) {
            $case_ids = Auth::user()->cases()
                                    ->staffOrPrivateAndMine()
                                      ->where('type', $input['case_type'])
                                      ->pluck('id');
                                      //dd($case_ids);

            $person_ids = WorkCasePerson::whereIn('case_id', $case_ids)
                                      ->pluck('person_id');

                                      //dd($person_ids);
            $people_collection = $people_collection->merge(Person::whereIn('id', $person_ids)->get());
            //dd($people_collection);
        }

        if (isset($input['ignore_tracker_code'])) {
            $emails = BulkEmail::where('team_id', Auth::user()->team->id)
                               ->where('old_tracker_code', $input['ignore_tracker_code'])
                               ->pluck('id');
            foreach ($emails as $the_email_id) {
                $recipients = BulkEmailQueue::where('bulk_email_id', $the_email_id)
                                            ->pluck('person_id');

                $people_collection = $people_collection->whereNotIn('id', $recipients);
            }
        }

        // ====================================================> REMOVE THOSE WITHOUT EMAILS?

        if ($mode == 'GET_MISSING_EMAILS_TOO') {
            $missing_emails_collection = $people_collection->filter(function ($model) {
                if (! $model->primary_email) {
                    return $model;
                }
            });
        }

        $people_collection = $people_collection->filter(function ($model) {
            if ($model->primary_email) {
                return $model;
            }
        });

        if (isset($input['age'])) {
            if ($input['age'] > 0) {
            $people_collection = Person::where('team_id', Auth::user()->team->id)
                                           ->where('dob', '<', Carbon::today()->subYears($input['age']))
                                           ->get();
            
            }
        }

        if (isset($input['municipalities'])) {
            $municipalities = $input['municipalities'];
            //dd($municipalities);
            if ($people_collection->count() > 0) {
                $people_collection = $people_collection->whereIn('city_code', $municipalities);
            } else {
                $people_collection = Person::where('team_id', Auth::user()->team->id)
                                           ->whereIn('city_code', $municipalities)
                                           ->get();
            }
            //dd($people_collection, $input['municipalities']);
        }

        // ====================================================> NO DEAD PEOPLE!!!!

        $people_collection = $people_collection->filter(function ($model) {
            if (!$model->deceased) {
                return $model;
            }
        });

        // ====================================================>
        if ($mode == 'GET_COUNT') {
            return $people_collection->count();
        }

        // ====================================================> ORDER BY
        if (isset($input['order_by'])) {

            // Blank is ASC
            if (isset($input['order_direction'])) {
                $people_collection = $people_collection->sortByDesc($input['order_by']);
            } else {
                $people_collection = $people_collection->sortBy($input['order_by']);
            }
        } else {
            $people_collection = $people_collection->sortBy('last_name');
        }
        $people_collection = $people_collection->unique();

        // ====================================================> RETURN

        if ($mode == 'GET_MISSING_EMAILS_TOO') {
            return ['people'            => $people_collection,
                    'missing_emails'    => $missing_emails_collection, ];
        } else {
            return $people_collection;
        }
    }

    public function livewire_ProcessCategoriesAndGroups($categories) 
    {
        $people_pivots = [];

        foreach($categories as $category_id => $group) {
            foreach($group as $group_id => $options) {
                foreach($options as $support_type => $data) {

                    if ($support_type == 'main') {

                        $person_ids = GroupPerson::where('group_id',$group_id)
                                            ->where('team_id',Auth::user()->team->id)
                                            
                                            ->pluck('person_id')
                                            ->toArray();

                    } else {

                        $person_ids = GroupPerson::where('group_id',$group_id)
                                            ->where('team_id',Auth::user()->team->id)
                                            
                                            ->where('position', $support_type)
                                            ->pluck('person_id')
                                            ->toArray();

                    }

                    $people_pivots = array_merge($people_pivots, $person_ids);

                }
            }
        }

        return Person::where('team_id', Auth::user()->team->id)
                     ->whereIn('id', $people_pivots)
                     ->pluck('id');
    }

    public function processCategoriesAndGroups($categories, $input, $people_collection) 
    {
        $new_people_collection = $people_collection;
        foreach ($categories as $cat) {
            $groups = $input['category_'.$cat->id];

            $people_pivots = [];

            // INCLUDE GROUPS ACCORDING TO SUPPORT/OPPOSED

            foreach ($groups as $thegroup) {
                if (substr($thegroup, -8) == '_opposed') {
                    $group_id = substr($thegroup, 0, -8);

                    $person_ids = GroupPerson::where('group_id', $group_id)
                                        ->where('team_id', Auth::user()->team->id)
                                        ->where('position', 'opposed')
                                        
                                        ->pluck('person_id')
                                        ->toArray();
                    $people_pivots = array_merge($people_pivots, $person_ids);
                } elseif (substr($thegroup, -9) == '_supports') {
                    $group_id = substr($thegroup, 0, -9);

                    $person_ids = GroupPerson::where('group_id', $group_id)
                                        ->where('team_id', Auth::user()->team->id)
                                        ->where('position', 'supports')
                                        
                                        ->pluck('person_id')
                                        ->toArray();
                    $people_pivots = array_merge($people_pivots, $person_ids);
                } else {
                    $group_id = $thegroup;

                    $person_ids = GroupPerson::where('group_id', $group_id)
                                        ->where('team_id', Auth::user()->team->id)
                                        
                                        ->pluck('person_id')
                                        ->toArray();
                    $people_pivots = array_merge($people_pivots, $person_ids);
                }
            }

            $cat_collection = Person::where('team_id', Auth::user()->team->id)->whereIn('id', $people_pivots)->get();

            $new_people_collection = $new_people_collection->merge($cat_collection);
        }

        return $new_people_collection;
    }

    public function startCountingWhenTriggeredBy($input)
    {

        //IF ANY OF THESE ARE GIVEN, START COUNTING

        $triggers = [
                     // 'first_name',
                     // 'last_name',
                     // 'street',
                     'age_operator',
                     'age',
                     'municipalities',
                     'zips',
                     'parties',
                     'has_received_emails',
                     'has_not_received_emails',
                    ];

        //APPEND CATEGORIES TO FIELD LIST
        $categories = Auth::user()->categories();
        foreach ($categories as $cat) {
            $triggers[] = 'category_'.$cat->id;
        }

        foreach ($triggers as $item) {
            if (isset($input[$item]) && ($input[$item] != null)) {
                return true;
            }
        }

        return false;
    }

    public function getQueryFormFields($request, $mode = null)
    {
        switch ($mode) {

            case 'bulkemail':
                $query_form = ['linked',
                                'first_name',
                                'last_name',
                                'street',
                                'age_operator',
                                'age',
                                'ignore_archived',
                                'order_by',
                                'master_email', // master_email_list = DB name
                                // 'order_direction',
                                'municipalities',
                                'zips',
                                'parties',
                                'case_type',
                                'has_received_emails',
                                'has_not_received_emails',
                                'has_not_received_codes',
                                'ignore_tracker_code',
                                'email_not_null',
                                'sales_type',                   // For Marketing App
                                'sales_clients_or_prospects',    // For Marketing App
                            ];

                //APPEND CATEGORIES TO FIELD LIST
                $categories = Auth::user()->categories()->get();
                foreach ($categories as $cat) {
                    $query_form[] = 'category_'.$cat->id;
                }

            break;

            default:
                $query_form = ['linked',
                                'first_name',
                                'middle_name',
                                'last_name',
                                'email',
                                'street',
                                'age_operator',
                                'age',
                                'ignore_archived',
                                'order_by',
                                'order_direction',
                                'municipalities',
                                'zips',
                                'parties',
                                'fields',
                                'master_email', // master_email_list = DB name
                                ];
            break;

        }

        // $partial_query_form  = array_filter($query_form, function($el) {
        //                             return ( strpos($el, '*') !== false );
        //                         });

        $terms = [];

        foreach ($request->all() as $key => $value) {
            if (in_array($key, $query_form)) {
                $terms[$key] = $value;
            }
        }

        return $terms;
    }

    public function getMunicipalities()
    {
        //dd();
        $city_codes =  Voter::withTrashed()
                            ->whereNull('archived_at')
                            ->groupBy('city_code')
                            ->pluck('city_code');

        $municipalities = Municipality::whereIn('code', $city_codes)
                                      ->orderBy('name')
                                      ->get();
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
                                     ->whereIn('code', $district_ids)
                                     ->where('state', session('team_state'))
                                     ->orderBy('sort')
                                     ->get();

            $districts = $districts->merge($add_districts);

        }
        
        return $districts;
    }

    public function getZips()
    {
        $zips = Voter::withTrashed()->distinct()->select('address_zip')->orderBy('address_zip')->pluck('address_zip');

        return $zips;
    }

    public function getGroupCategoriesFromInput($input)
    {
        $categories = [];

        if ($input) {
            foreach ($input as $key => $value) {
                if (substr($key, 0, 9) == 'category_') {
                    $categories[] = substr($key, 9);
                }
            }
        }

        return Category::whereIn('id', $categories)->get();
    }

    public function getPeopleFromName($name)
    {
        $people = null;
        $queryarr = explode(' ', trim($name));

        if (count($queryarr) == 1) {
            // could be first or last name
            $input = [];
            $input['linked'] = true;
            $input['first_name'] = $name;
            $fpeople = $this->constituentQuery($input, 3);

            $input = [];
            $input['linked'] = true;
            $input['last_name'] = $name;
            $lpeople = $this->constituentQuery($input, 3);

            $people = $fpeople->merge($lpeople);
        } elseif (count($queryarr) > 1) {
            // has first and last
            $input = [];
            $input['linked'] = true;
            $input['first_name'] = $queryarr[0];
            $input['last_name'] = $queryarr[1];
            if (isset($queryarr[2])) {
                $input['last_name'] .= ' '.$queryarr[2];
            }
            $people = $this->constituentQuery($input, 5);
        }

        return $people;
    }

    public function getPeopleAndVotersFromName($name)
    {
        $people = null;
        $queryarr = explode(' ', trim($name));

        if (count($queryarr) == 1) {
            // could be first or last name
            $input = [];
            $input['first_name'] = $name;
            $fpeople = $this->constituentQuery($input, 3);

            $input = [];
            $input['last_name'] = $name;
            $lpeople = $this->constituentQuery($input, 3);

            $people = $fpeople->merge($lpeople);
        } elseif (count($queryarr) > 1) {
            // has first and last
            $input = [];
            $input['first_name'] = $queryarr[0];
            $input['last_name'] = $queryarr[1];
            if (isset($queryarr[2])) {
                $input['last_name'] .= ' '.$queryarr[2];
            }
            $people = $this->constituentQuery($input, 5);
        }

        return $people;
    }

    public function constituentQuery($input, $limit = null, $fields = null, $start_from_zero = null)
    {
        //dd($input);
        // ====================================================> SET UP
        if (isset($input['limit'])) {
            $limit = $input['limit'];
        } elseif ($limit == 'none') {
            $limit = null;
        } elseif (! $limit) {
            $limit = (request('per_page')) ? request('per_page') : 100; // Defaults to 100
        }

        $start = microtime(-1);

        if (Person::first()) {
            $people_keys = array_keys(Person::first()->toArray());
        }
        if (Voter::first()) {
            $voter_keys = array_keys(Voter::first()->toArray());
        }
        //dd("Laz");

        // ====================================================> START FROM ZERO

        if ($start_from_zero) {
            $check = $this->startCountingWhenTriggeredBy($input);

            if (! $check) {
                $people = Person::where('id', 'THIS_ID_DOES_NOT_EXIST');
                $voters = Voter::where('id', 'THIS_ID_DOES_NOT_EXIST');
            }
        }

        // ====================================================> START FROM ALL (+SELECT FIELDS)

        if (! isset($people) || ! isset($voters)) {
            if (! $fields) {
                $people = Person::where('team_id', Auth::user()->team->id);
                $voters = Voter::withTrashed();
            }

            if ($fields) {
                $people = Person::where('team_id', Auth::user()->team->id);
                $voters = Voter::withTrashed();

                $people_fields = $fields;
                if (! in_array('id', $people_fields)) {
                    $people_fields[] = 'id';
                }
                if (! in_array('voter_id', $people_fields)) {
                    $people_fields[] = 'voter_id';
                }

                $voter_fields = $fields;
                if (! in_array('id', $voter_fields)) {
                    $voter_fields[] = 'id';
                }
            }
        }

        // ====================================================> MASTER EMAIL

        if (isset($input['master_email'])) {
            if ($input['master_email'] == 'on') {
                $people->where('master_email_list', true);
                $input['linked'] = true; // Only People No Voter
            }
        }

        if (isset($input['voter_has_email'])) {
            //dd("Laz");
            $voters->where('emails', '!=', '[]');
        }

        // ====================================================> GROUPS

        if (!isset($input['using_livewire'])) {

            $categories = $this->getGroupCategoriesFromInput($input);

            if ($categories->first()) {
                $input['linked'] = true;
                $people_collection = $this->processCategoriesAndGroups($categories, $input, collect([]));
                $people->whereIn('id', $people_collection->pluck('id'));
            }

        } else {

            if ($input['category'] && !empty($input['category'])) {
                $input['linked'] = true;
                $people_collection = $this->livewire_processCategoriesAndGroups($input['category']);
                $people->whereIn('id', $people_collection);
            }

        }



        // ====================================================> VOTERS LINKED/IGNORED

        if (!isset($input['using_livewire'])) {

            if (isset($input['linked'])) {
                $voters->where('id', 'THIS_ID_DOES_NOT_EXIST');
            }

        } else {

            if ($input['linked']) {
                $voters->where('id', 'THIS_ID_DOES_NOT_EXIST');
            }
        }

        // Confusing because form phrased in reverse: "with archived"
        if (isset($input['ignore_archived'])) {
            $voters->whereNull('archived_at');
            $people->where('deceased', '<>', true);
        }

        // ====================================================> FIRST NAME
        if (isset($input['first_name']) && $input['first_name']) {
            $first_name = str_replace('*', '%', $input['first_name']);
            $voters->where('first_name', 'LIKE', $first_name.'%');
            $people->where('first_name', 'LIKE', $first_name.'%');
        }
        // ====================================================> LAST NAME
        if (isset($input['last_name']) && $input['last_name']) {
            $last_name = str_replace('*', '%', $input['last_name']);
            $voters->where('last_name', 'LIKE', $last_name.'%');
            $people->where('last_name', 'LIKE', $last_name.'%');
        }

        // ====================================================> MIDDLE NAME
        if (isset($input['last_name']) && $input['last_name']) {
            if (isset($input['middle_name']) && $input['middle_name']) {
                $middle_name = $input['middle_name'];
                $voters->where('middle_name', 'LIKE', $middle_name.'%');
                $people->where('middle_name', 'LIKE', $middle_name.'%');
            }
        }

        // ====================================================> EMAIL

        if (isset($input['email']) && ($input['email'])) {
            $input['email'] = preg_replace('/'.'[^a-zA-Z0-9@._-]'.'/', '', $input['email']);

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
            $voters = Voter::where('id', 'THIS_ID_DOES_NOT_EXIST'); // Do not search voters
        }

        // ====================================================> PHONES

        if (isset($input['phone']) && ($input['phone'])) {

            $phone_search = phoneOnlyNumbers($input['phone']);
            $first_three = substr($phone_search, 0, 3);
            $last_four = substr($phone_search, -4);

            //////// PRIMARY AND WORK PHONE

            $phones_found = [];

            foreach(['primary_phone', 'work_phone'] as $phone_type) {

                $results = Person::where('team_id', Auth::user()->team->id)
                                       ->where($phone_type, 'like', '%'.$last_four)
                                       ->where($phone_type, 'like', '%'.$first_three.'%')
                                       ->take(20)
                                       ->get();

                foreach($results as $result) {
                    if (phoneOnlyNumbers($result->$phone_type) == $phone_search) {
                        $phones_found[] = $result->id;
                    }
                }

            }

            //////// OTHER_PHONES ARRAY, FORMAT: [["6171234567","Home"]]

            $results = Person::where('team_id', Auth::user()->team->id)
                       ->where('other_phones', 'like', '%'.$last_four.'%')
                       ->where('other_phones', 'like', '%'.$first_three.'%')
                       ->take(20)
                       ->get();

            foreach($results as $result) {
                $phones_found[] = $result->id;
            }

            ////// GET PEOPLE FROM PHONES

            $people->whereIn('id', $phones_found); 

            ////// VOTER PHONES -- ADD LATER

            $voters->where('home_phone', 'like', $phone_search.'%')->take(20);

        }

        // ====================================================> STREET NAME
        if (isset($input['street'])) {
            $street = str_replace('*', '%', $input['street']);
            $street_num = preg_replace('/\D/', '', $street);
            $street_name = trim(preg_replace('/\d/', '', $street));
            //dd($street, $name, $street_num);
            if ($street_num) {
                $voters->where('address_number', $street_num);
                $people->where('address_number', $street_num);
            }
            if ($street_name) {
                $voters->where('address_street', 'LIKE', $street_name.'%');
                $people->where('address_street', 'LIKE', $street_name.'%');
            }
        }

        // ====================================================> MUNICIPALITIES
        if (isset($input['municipalities'])) {
            $municipalities = $input['municipalities'];
            //dd($municipalities);
            $voters->whereIn('city_code', $municipalities);
            $people->whereIn('city_code', $municipalities);
        }

        // ====================================================> DISTRICTS
        if (isset($input['congress_districts'])) {
            $district_ids = $input['congress_districts'];
            $f_districts = District::whereIn('id', $district_ids)->where('type', 'F')->pluck('code');
            if ($f_districts->first()) {
                $voters->whereIn('congress_district', $f_districts);
                $people->whereIn('congress_district', $f_districts);
            }
        }
        if (isset($input['senate_districts'])) {
            $district_ids = $input['senate_districts'];
            $s_districts = District::whereIn('id', $district_ids)->where('type', 'S')->pluck('code');
            if ($s_districts->first()) {
                $voters->whereIn('senate_district', $s_districts);
                $people->whereIn('senate_district', $s_districts);
            }
        }
        if (isset($input['house_districts'])) {
            $district_ids = $input['house_districts'];
            $h_districts = District::whereIn('id', $district_ids)->where('type', 'H')->pluck('code');
            if ($h_districts->first()) {
                $voters->whereIn('house_district', $h_districts);
                $people->whereIn('house_district', $h_districts);
            }
        }

        // ====================================================> ZIP CODES
        if (isset($input['zips'])) {
            $zips = $input['zips'];
            $voters->whereIn('address_zip', $zips);
            $people->whereIn('address_zip', $zips);
        }

        // ====================================================> PARTY
        if (isset($input['parties'])) {
            $parties = collect($input['parties']);
            if ($parties) {
                foreach ($parties as $party) {
                    if ($party == 'Other') {
                        $parties->merge(['B', 'C', 'I']);
                    }
                }
                $voters->whereIn('party', $parties);
                $people->whereIn('party', $parties);
            }
        }

        // ====================================================> AGE
        if (isset($input['age_operator'])) {
            $age_operator = $input['age_operator'];

            if ($age_operator == 'UNKNOWN') {
                $voters->whereNull('dob');
                $people->whereNull('dob');
            } else {
                if (isset($input['age'])) {
                    $age = preg_replace('/[^\d-]+/', '', $input['age']);
                    if ($age) {
                        if ($age_operator == '=') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $end_date = Carbon::today()->subYears($age);
                            $voters->where('dob', '>', $start_date);
                            $voters->where('dob', '<', $end_date);
                            $people->where('dob', '>', $start_date);
                            $people->where('dob', '<', $end_date);
                        } elseif ($age_operator == '>') {
                            $start_date = Carbon::today()->subYears($age + 1);
                            $voters->where('dob', '<', $start_date);
                            $people->where('dob', '<', $start_date);
                        } elseif ($age_operator == '<') {
                            $start_date = Carbon::today()->subYears($age);
                            $voters->where('dob', '>', $start_date);
                            $people->where('dob', '>', $start_date);
                        } elseif ($age_operator == 'RANGE') {
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
        if (isset($input['email_not_null'])) {
            $people->whereNotNull('primary_email');
        }

        // ====================================================> HAS RECEIVED EMAILS

        if (isset($input['has_received_emails'])) {
            $emails = collect($input['has_received_emails']);
            foreach ($emails as $the_email_id) {
                $recipients_people = BulkEmailQueue::where('bulk_email_id', $the_email_id)
                                                   ->pluck('person_id')
                                                   ->toArray();

                $recipients_voters = BulkEmailQueue::where('bulk_email_id', $the_email_id)
                                                   ->whereNotNull('voter_id')
                                                   ->pluck('voter_id')
                                                   ->toArray();

                $people->whereNotIn('id', $recipients_people);
                $voters->whereNotIn('id', $recipients_voters);
            }
        }

        // ====================================================> PEOPLE + VOTERS + COUNT

        if (count($input) < 1) {    // All Constituents
            $this->total_count = Auth::user()->team->constituents_count;
            $this->total_count_people = $people->count();
            $this->total_count_voters = $this->total_count - $this->total_count_people;
        // $this->total_count_voters = Voter::whereNotIn('id',
            //                             (clone $people)->whereNotNull('voter_id')
            //                                            ->pluck('voter_id')->toArray()
            //                             )->count();
        } elseif (isset($input['linked'])) { // People Only

            $this->total_count = $people->count();
            $this->total_count_people = $this->total_count;
            $this->total_count_voters = null;
        } else {
            $this->total_count_voters = (clone $voters)->whereNotIn('id',
                                                    (clone $people)->whereNotNull('voter_id')
                                                                   ->pluck('voter_id')
                                                        ->toArray())->count();
            $this->total_count_people = (clone $people)->count();
            $this->total_count = $this->total_count_people + $this->total_count_voters;
        }

        // ====================================================> ORDER BY
        if (isset($input['order_by'])) {
            if ($input['order_by'] == 'dob') {
                // "Age" makes more sense reversed asc/desc
                if (isset($input['order_direction'])) {
                    unset($input['order_direction']);
                } else {
                    $input['order_direction'] = 'desc';
                }
            }
            // Blank is ASC
            if (isset($input['order_direction'])) {
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
        //dd($input);
        if (isset($input['all_people'])) {
            $people_collection = $people->get();
        } else {
            if ($limit) {
                $people_collection = $people->take($limit)->get();
            }
            if (! $limit) {
                $people_collection = $people->get();
            }
            // $people_collection = $people->get();
        }

        //dd($people_collection->count());

        $valid_voterids = [];
        foreach ($people_collection->pluck('voter_id') as $vid) {
            if ($vid) {
                $valid_voterids[] = $vid;
            }
        }

        $voters_collection = $voters->whereNotIn('id', $valid_voterids);

        if ($limit) {
            $voters_collection = $voters_collection->take($limit)->get();
        }
        if (! $limit) {
            $voters_collection = $voters_collection->get();
        }

        // ====================================================> TO DISTINGUISH IN VIEWS

        if (! $fields) { // I.E. THIS IS NOT AN EXPORT

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
        if ($limit) {
            $constituents = $constituents->take($limit);
        }
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
            $constituents->transform(function ($i) {
                unset($i->id);
                unset($i->voter_id);

                return $i;
            });
        }

        // ====================================================> RETURN

        $time = microtime(-1) - $start;

        Log::stack(['query_timer'])->info('TIME: '.$time, $input);

        return $constituents;
    }
}
