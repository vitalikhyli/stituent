<?php

namespace App\Traits;

use App\CampaignList;
use App\District;
use App\Participant;
use App\Voter;
use Auth;

trait ParticipantQueryTrait
{
    public function prepareInput($input)
    {
        if (is_array($input)) {
            $cycle_through_this = $input;
        } else {
            $cycle_through_this = $input->query();
        }

        $save_array = [];

        foreach ($cycle_through_this as $key => $value) {
            $skip = ['_token',
                     'save_query_as',
                     'use_query',
                     'change_query',
                    ];

            if (in_array($key, $skip)) {
                continue;
            }

            $save_array[$key] = $value;
        }

        return $save_array;
    }

    public function participantQuery($input, $limit = null)
    {

        ////////////////////////////////////////////////////////////////////////////
        //
        //  HANDLE FORM FILTERS, SAVES, ETC
        //

        if (isset($input['limit'])) {
            $limit = $input['limit'];
        } elseif ($limit == 'none') {
            $limit = null;
        } elseif (! $limit) {
            $limit = (request('per_page')) ? request('per_page') : 100; // Defaults to 100
        }



        // if (isset($input['save_query_as']) && ($input['save_query_as'] != null)) {

        //     if (CampaignList::where('team_id', Auth::user()->team->id)
        //                      ->where('name',$input['save_query_as'])
        //                      ->doesntExist()
        //         ) {
        //             $query = new CampaignList;
        //             $query->team_id     = Auth::user()->team->id;
        //             $query->user_id     = Auth::user()->id;
        //             $query->name        = $input['save_query_as'];
        //             $query->form       = $this->prepareInput($input);
        //             $query->save();

        //             $_GET['use_query'] = $query->id;
        //         }

        // } elseif (isset($input['change_query']) && isset($input['use_query'])) {

        //         $query = CampaignList::find($input['use_query']);

        //         if ($query) {
        //             $new_input = $this->prepareInput($query->form);

        //             // unset($_GET);
        //             // unset($input);

        //             foreach($new_input as $key => $value) {
        //                 $_GET[$key] = $value;
        //                 $input[$key] = $value;
        //             }

        //             $_GET['use_query'] = $query->id;
        //             $input['use_query'] = $query->id;
        //             $_GET['mode_advanced'] = true;
        //             $input['mode_advanced'] = true;
        //         }

        // }

        //////////////////////////////////////////////////////////////////////////////////////
        // Check to see if form matches a saved query and if so load it in the select drop-down

        // $serialized_input = json_encode($this->prepareInput($input));
        // $current = CampaignList::where('form',$serialized_input)->first();

        // if (!$current) {
        //     $_GET['use_query'] = $input['use_query'] = null;
        // } else {
        //     $_GET['use_query'] = $input['use_query'] = $current->id;
        // }

        //////////////////////////////////////////////////////////////////////////////
        //
        //  BUILD QUERY
        //

        //////////////////////////////////////////////////////////////////////////////
        // Generate strings to find in Solr
        //
        // Solr string: since:2020-statewide:0
        // Form logic:  (Type = Local) Voted in >/</= 4 elections since 2010
        //

        // $max_to_check = 10; // High number of elections to check in terms of participation

        // if (isset($input['participation_L_equate']) &&
        //     isset($input['participation_L_num']) &&
        //     isset($input['participation_L_year'])
        //     ){

        //     $type   = 'local';
        //     $equate = $input['participation_L_equate'];
        //     $num    = $input['participation_L_num'];
        //     $year   = $input['participation_L_year'];

        //     $strings_to_search = [];

        //     if ($equate == 'Exactly') {
        //         $strings_to_search[] = 'since:'.$year.'-'.$type.':'.$num;
        //     }

        //     if ($equate == 'Fewer than') {
        //         for($num_pointer = $num -1; $num_pointer >= 0; $num_pointer--) {
        //             $strings_to_search[] = 'since:'.$year.'-'.$type.':'.$num_pointer;
        //         }
        //     }

        //     if ($equate == 'More than') {
        //         for($num_pointer = $num +1; $num_pointer <= $max_to_check; $num_pointer++) {
        //             $strings_to_search[] = 'since:'.$year.'-'.$type.':'.$num_pointer;
        //         }
        //     }

        //     dd($equate, $num, $year, $strings_to_search);
        // }

        //////////////////////////////////////////////////////////////////////////////

        $campaign = CurrentCampaign();
        $participants = Participant::where('participants.team_id', Auth::user()->team->id);

        //////////////////////////////////////////////////////////////////////////////

        // if (isset($input['support']) && ($input['support'] != null)) {
        //     $_GET['scope']  = 'participants_only';
        //     $input['scope'] = 'participants_only';
        // }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['ids']) && $input['ids']) {
            $arr = json_decode($input['ids']);
            $participants->whereIn('id', $arr);
            $input['scope'] = 'participants_only';
            //dd($participants->toSql());
            // $participants
        }

        if (isset($input['scope']) && $input['scope'] == 'participants_only') {

            $voters = Voter::where('id', 'SLOTHES_EXIST_BUT_THIS_ID_DOES_NOT');

        } else {

            $participants_voter_ids = $participants->pluck('voter_id')->toArray();
            $voters = Voter::query();
        }

        if (!request('include_archived')) {
            $voters->whereNull('archived_at');
        }

        //////////////////////////////////////////////////////////////////////////////////////
        //
        // EXCLUSIONS
        //

        // $removed    = Participant::thisTeam()->where('go_away', true)->get();
        // $deceased   = Participant::thisTeam()->where('deceased', true)->get();
        // $excluded   = $removed->merge($deceased);

        // $participants->whereNotIn('id', $excluded->pluck('id'));
        // $voters->whereNotIn('id', $excluded->pluck('voter_id'));
    
        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['search'])) {

            $search = $input['search'];
            $participants->where(function ($q) use ($search) {
                $q->orWhere('first_name', 'like', $search.'%');
                $q->orWhere('last_name', 'like', $search.'%');
            });

            $search_arr = explode(' ', $search);

            if (count($search_arr) == 1) {

                $voters->where(function ($q) use ($search) {
                    $q->orWhere('first_name', 'like', $search.'%');
                    $q->orWhere('last_name', 'like', $search.'%');
                });
            } elseif (count($search_arr) == 2) {
                $fname = $search_arr[0];
                $lname = $search_arr[1];
                $voters = $voters->where('first_name', 'LIKE', $fname.'%');
                $voters = $voters->where('last_name', 'LIKE', $lname.'%');

            } elseif (count($search_arr) > 2) {
                $voters->where(function ($q) use ($search) {
                    $q->where('full_name', 'LIKE', $search.'%')
                      ->orWhere('full_address', 'LIKE', $search.'%');
                });
            }
        }


        // ====================================================> FIRST NAME
        if (isset($input['first_name']) && $input['first_name']) {
            $first_name = str_replace('*', '%', $input['first_name']);
            $voters->where('first_name', 'LIKE', $first_name.'%');
            $participants->where('first_name', 'LIKE', $first_name.'%');
        }
        // ====================================================> LAST NAME

        if (isset($input['last_name']) && $input['last_name']) {
            $last_name = str_replace('*', '%', $input['last_name']);
            $voters->where('last_name', 'LIKE', $last_name.'%');
            $participants->where('last_name', 'LIKE', $last_name.'%');
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['email']) && ($input['email'])) {

            $input['email'] = preg_replace('/'.'[^a-zA-Z0-9@._-]'.'/', '', $input['email']);

            $personal_emails = Participant::where('team_id', Auth::user()->team->id)
                             ->where('primary_email', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $work_emails = Participant::where('team_id', Auth::user()->team->id)
                             ->where('work_email', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $other_emails = Participant::where('team_id', Auth::user()->team->id)
                             ->where('other_emails', 'LIKE', '%'.$input['email'].'%')
                             ->take(5)
                             ->get();

            $emails = $personal_emails->merge($work_emails)->merge($other_emails);

            $participants->whereIn('id', $emails->pluck('id')->toArray());

            $voters = Voter::where('id', 'SLOTHES_EXIST_BUT_THIS_ID_DOES_NOT'); // Do not search voters
        }

        // ====================================================> PHONES

        if (isset($input['phone']) && ($input['phone'])) {

            $phone_search = phoneOnlyNumbers($input['phone']);
            $first_three = substr($phone_search, 0, 3);
            $last_four = substr($phone_search, -4);

            //////// PRIMARY AND WORK PHONE

            $phones_found = [];

            foreach(['primary_phone', 'work_phone'] as $phone_type) {

                $results = Participant::where('team_id', Auth::user()->team->id)
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

            $results = Participant::where('team_id', Auth::user()->team->id)
                       ->where('other_phones', 'like', '%'.$last_four.'%')
                       ->where('other_phones', 'like', '%'.$first_three.'%')
                       ->take(20)
                       ->get();

            foreach($results as $result) {
                $phones_found[] = $result->id;
            }

            ////// GET PEOPLE FROM PHONES

            $participants->whereIn('id', $phones_found); 

            ////// VOTER PHONES -- ADD LATER

            $voters = Voter::where('id', 'THIS_ID_DOES_NOT_EXIST'); // Do not search voters  
        }

        //////////////////////////////////////////////////////////////////////////////////////

        // if (isset($input['city']) && ($input['city'] != null)) {
        //     $city = request('city');
        //     $participants->where('address_city', $city);
        //     $voters->where('address_city', $city);
        // }

        if (isset($input['municipalities'])) {
            $municipalities = $input['municipalities'];
            $voters->whereIn('city_code', $municipalities);
            $participants->whereIn('city_code', $municipalities);
        }
        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['street']) && ($input['street'] != null)) {
            $street = request('street');
            $participants->where('address_street', 'like', '%'.$street.'%');
            $voters->where('address_street', 'like', '%'.$street.'%');
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['party']) && ($input['party'] != null)) {
            $party = request('party');
            $participants->where('party', $party);
            $voters->where('party', $party);
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['gender']) && ($input['gender'] != null)) {
            $gender = request('gender');
            $participants->where('gender', $gender);
            $voters->where('gender', $gender);
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['ward']) && ($input['ward'] != null)) {
            $ward = request('ward');
            $participants->where('ward', $ward);
            $voters->where('ward', $ward);
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['precinct']) && ($input['precinct'] != null)) {
            $precinct = request('precinct');
            $participants->where('precinct', $precinct);
            $voters->where('precinct', $precinct);
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['congress_districts'])) {
            $district_ids = $input['congress_districts'];

            $f_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'F')->pluck('code');
            if ($f_districts->first()) {
                $voters->whereIn('congress_district', $f_districts);
                $participants->whereIn('congress_district', $f_districts);
            }
        }
        if (isset($input['senate_districts'])) {
            $district_ids = $input['senate_districts'];
            $s_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'S')->pluck('code');
            if ($s_districts->first()) {
                $voters->whereIn('senate_district', $s_districts);
                $participants->whereIn('senate_district', $s_districts);
            }
        }
        if (isset($input['house_districts'])) {
            $district_ids = $input['house_districts'];
            $h_districts = District::where('state', session('team_state'))->whereIn('id', $district_ids)->where('type', 'H')->pluck('code');
            if ($h_districts->first()) {
                $voters->whereIn('house_district', $h_districts);
                $participants->whereIn('house_district', $h_districts);
            }
        }

        //////////////////////////////////////////////////////////////////////////////////////

        if (isset($input['support']) && ($input['support'] != null)) {
            $support = request('support');

            $equate = request('support-equate');
            if ($equate == 'eq') {
                $equate_symbol = '=';
            }
            if ($equate == 'gt') {
                $equate_symbol = '>';
            }
            if ($equate == 'lt') {
                $equate_symbol = '<';
            }

            if ($support == 'any') {
                $equate_symbol = '>';
                $support = 0;
            }

            $participants->join('campaign_participant',
                                'participants.id',
                                '=',
                                'campaign_participant.participant_id')
                         ->where('campaign_participant.campaign_id', $campaign->id)
                         ->where('campaign_participant.support', $equate_symbol, $support);
        }

        //////////////////////////////////////////////////////////////////////////////////////

        $participants_collection = $participants->get()->map(function ($row) {
            $row['is_participant'] = true;

            return $row;
        });

        //////////////////////////////////////////////////////////////////////////////////////


        if ($limit) {
            $participants_collection = $participants_collection->take($limit);
        }
        if ($limit) {
            $voters->take($limit);
        }

        $voters = $voters->whereNotIn('id', $participants_collection->pluck('voter_id'));

        $voters_collection = $voters->get()->map(function ($row) {
            $row['voter_id'] = $row['id'];
            $row['is_voter'] = true;

            return $row;
        });

        //////////////////////////////////////////////////////////////////////////////////////

        $participants = $participants_collection->merge($voters_collection)
                                                ->sortBy('first_name') // Does not sort correctly
                                                ->sortBy('last_name');

        //////////////////////////////////////////////////////////////////////////////////////

        return $participants;
    }
}
