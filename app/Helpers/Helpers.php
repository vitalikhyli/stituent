<?php

use App\Campaign;
use App\Action;
use App\Participant;
use App\Person;
use App\Team;
use App\User;
use App\Voter;
use App\VoterMaster;
use App\VotingHouseholdMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

    function VolunteerSession()
    {
        $guest = App\Models\Campaign\Volunteer::find(session('guest'));
        return $guest ?? null;
    }
    function emailRegexPattern()
    {
        // OLD
        // return '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        return '/[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})/i';
    }
    function remove_bs($Str) {  
      $StrArr = str_split($Str); $NewStr = '';
      foreach ($StrArr as $Char) {    
        $CharNo = ord($Char);
        if ($CharNo == 163) { $NewStr .= $Char; continue; } // keep £ 
        if ($CharNo > 31 && $CharNo < 127) {
          $NewStr .= $Char;    
        }
      }  
      return $NewStr;
    }

    function addActionFromObject($obj, $action_name, $details, $at = null, $added_by = null) 
    {
        try {
            //echo $obj->id.": Adding $action_name, $details \n";
            $at_field = 'created_at';
            if ($at) {
                $at_field = $at;
            }
            $participant_id = $obj->participant_id;
            //dd(get_class($obj));
            if (get_class($obj) == 'App\Participant') {
                $participant_id = $obj->id;
            }
            $action = Action::where('participant_id', $participant_id)
                            ->where('name', $action_name)
                            ->where('created_at', $obj->$at_field)
                            ->first();
            //dd($action);
            if (!$action) {
                $last_five_minutes = Action::where('participant_id', $participant_id)
                                           ->where('name', $action_name)
                                           ->where('created_at', '>', Carbon::now()->subMinutes(5))
                                           ->first();

                if ($last_five_minutes) {
                    return;
                }
                $action = new Action;
                $action->user_id = $obj->user_id;
                $action->team_id = $obj->team_id;
                if (CurrentCampaign()) {
                    $action->campaign_id = CurrentCampaign()->id;
                } else {
                    $campaign = Campaign::where('team_id', $obj->team_id)
                                        ->where('current', true)
                                        ->first();
                    if ($campaign) {
                        $action->campaign_id = $campaign->id;
                    }
                }
                $action->participant_id = $participant_id;
                $action->voter_id = $obj->voter_id;
                $action->name = $action_name;
                $action->details = $details;
                if ($obj->$at_field) {
                    $action->created_at = $obj->$at_field;
                }
                $action->auto = true;
                //dd($action);
                $action->added_by = $added_by;
                $action->save();

            }
            return $action;
        } catch (\Exception $e) {
            echo "Error: couldn't add Action ".$e->getMessage();
            //dd($obj, $action_name, $details, $at);
        }
    }
    function addCustomActionToParticipant($participant, $action_name, $details, $added_by = null, $auto = false) 
    {
        try {
            $last_five_minutes = Action::where('participant_id', $participant->id)
                                       ->where('name', $action_name)
                                       ->where('created_at', '>', Carbon::now()->subMinutes(1))
                                       ->first();
            if ($last_five_minutes) {
                $action = $last_five_minutes;
            } else {
                $action = new Action;
            }
            
            if (Auth::user()) {
                $action->user_id = Auth::user()->id;
                $action->team_id = Auth::user()->team->id;
            } else {
                $action->user_id = $obj->user_id;
                $action->team_id = $obj->team_id;
            }
            
            if (CurrentCampaign()) {
                $action->campaign_id = CurrentCampaign()->id;
            } else {
                $campaign = Campaign::where('team_id', $obj->team_id)
                                    ->where('current', true)
                                    ->first();
                if ($campaign) {
                    $action->campaign_id = $campaign->id;
                }
            }
            $action->participant_id = $participant->id;
            $action->voter_id = $participant->voter_id;
            $action->name = $action_name;
            $action->details = $details;
            $action->auto = $auto;
            $action->added_by = $added_by;
            $action->save();

            return $action;
        } catch (\Exception $e) {
            echo "Error: couldn't add Custom Action ".$e->getMessage();
            //dd($obj, $action_name, $details, $at);
        }
    }
    function phoneOnlyNumbers($phone)
    {
        foreach(['.', '-', '(', ')', '+', ' '] as $char) {
            $phone = str_replace($char, '', $phone);
        }
        return $phone;        
    }

    function getParticipants()
    {
        if (session('participants')) {
            return session('participants');
        }
        updateParticipants();

        return session('participants');
    }
    function isParticipant($voter)
    {
        $participants = getParticipants();
        if (isset($participants[$voter->id])) {
            return true;
        }
        if (isset($participants[$voter->voter_id])) {
            return true;
        }
        return false;
    }
    function getParticipant($voter)
    {
        $participants = getParticipants();
        if (isset($participants[$voter->id])) {
            // dd($voter->id, $participants[$voter->id]);
            //echo $voter->id;
            $part = Participant::with(['tags'])
                               ->where('id', $participants[$voter->id])
                               ->first();
            return $part;
        }
        if (isset($participants[$voter->voter_id])) {
            $part = Participant::with(['tags'])
                               ->where('id', $participants[$voter->voter_id])
                               ->first();
            return $part;

        }

        return null;
    }
    function updateParticipants()
    {

        if (!Auth::user()) {
            return;
        }
        $participants_collection = Auth::user()->team
                                               ->participants()
                                               ->select('id', 'voter_id')
                                               ->get();
        //dd($participants_collection);
        $participants = collect([]);
        foreach ($participants_collection as $participant) {
            $key = $participant->voter_id;
            if (! $key) {
                $key = $participant->id;
            }
            $participants[$key] = $participant->id;
        }
        session(['participants' => $participants]);
    }

    function arrayToURL($array)
    {
        $amp = '?';
        $string = null;
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $element) {
                    $string .= $amp.$key.'[]='.$element;
                }
            } else {
                $string .= $amp.$key.'='.$value;
            }
            $amp = '&';
        }
        $string = (! $string) ? '?' : $string.$amp;

        return $string;
    }

    function FluencyPaginate($pages)
    {
        // Deal with search query URL string

        $string = arrayToURL(request()->input());

        // Build paginate navigation

        if ($pages['real_count'] > 0) {
            if ($pages['each'] > $pages['real_count']) {
                $pages['each'] = $pages['real_count'];
            }
            $showing = ($pages['current'] * $pages['each'] > $pages['real_count']) ? $pages['real_count'] : $pages['current'] * $pages['each'];

            echo 'Showing '.($showing).' of '.($pages['real_count']);

            if ($pages['current'] != 1) {
                echo ' - <a href="'.$string.'page=1">First</a> |';
            } else {
                echo '<span class="text-grey"> - First | </span>';
            }

            if ($pages['current'] > 1) {
                echo ' <a href="'.$string.'page='.($pages['current'] - 1).'">Previous</a>';
            } else {
                echo '<span class="text-grey">Previous</span>';
            }

            if ($pages['current'] * $pages['each'] < $pages['real_count']) {
                echo ' | <a href="'.$string.'page='.($pages['current'] + 1).'">Next</a>';
            } else {
                echo ' | <span class="text-grey">Next</span>';
            }

            if ($pages['current'] != $pages['total']) {
                echo ' | <a href="'.$string.'page='.$pages['total'].'">Last</a>';
            } else {
                echo ' | <span class="text-grey">Last</span>';
            }
        }
    }

    function checkedIfInArray($element, $array, $index)
    {
        if (isset($array[$index])) {
            if (in_array($element, $array[$index])) {
                return 'checked';
            }
        }
    }

    function selectedIfInArray($element, $array, $index)
    {
        if (isset($array[$index])) {
            if (in_array($element, $array[$index])) {
                return 'selected';
            }
        }
    }

    // Note on selectedIfInArray():
    //
    // in bulkemail view, requires addition of ."" in the following
    //
    // {{ selectedIfInArray($group->id."", $input, 'category_'.$cat->id) }}
    //
    // ...or somehow in_array() gets wrong item -- because group_id = a #?
    //
    //

    function selectedIfValueIs($value, $array, $index)
    {
        if (isset($array[$index])) {
            if ($array[$index] == $value) {
                return 'selected';
            }
        }
    }

    function createNewUserOnTeam($team, $username)
    {
        $newuser = new User;
        $newuser->current_team_id = $team->id;
        $newuser->username = $username;
        $newuser->name = $username;
        $newuser->email = 'placeholder.'.$username.'.'.Str::random(5).'.@communityfluency.com';
        $newuser->password = bcrypt('no_password');
        $newuser->active = false;
        $newuser->save();

        return $newuser;
    }

    function checkedIfValueIs($value, $array, $index, $default = null)
    {
        if (isset($array[$index])) {
            if ($array[$index] == $value) {
                return 'checked';
            } elseif ($default == true) {
                return 'checked';
            }
        } elseif ($default == true) {
            return 'checked';
        }
    }

    function SupportNumberToEnglish($n)
    {
        if ($n == 1) {
            return 'Yes';
        }
        if ($n == 2) {
            return 'Lean Yes';
        }
        if ($n == 3) {
            return 'Undecided';
        }
        if ($n == 4) {
            return 'Lean No';
        }
        if ($n == 5) {
            return 'No';
        }
        if ($n == null) {
            return null;
        }
    }
    function getSupportClass($support)
    {
        $supportclass = '';
        if ($support == 1) {
            $supportclass = 'bg-green';
        }
        if ($support == 2) {
            $supportclass = 'bg-yellow-dark';
        }
        if ($support == 3) {
            $supportclass = 'bg-orange';
        }
        if ($support == 4) {
            $supportclass = 'bg-red';
        }
        if ($support == 5) {
            $supportclass = 'bg-red-dark';
        }

        return $supportclass;
    }

    function CurrentCampaign()
    {
        
        if (session('current_campaign')) {
            //dd("Laz2");
            return session('current_campaign');
        }
        if (Auth::user()) {
            //dd("Laz3");
            $campaign = Campaign::where('team_id', Auth::user()->team_id)
                           ->where('current', true)
                           ->first();
            //dd($campaign);
            session(['current_campaign' => $campaign]);
            //dd("Laz");
            return $campaign;
        }
    }

    function IDisPerson($id)
    {
        if (is_numeric($id)) {
            return true;
        } else {
            return false;
        }
    }

    function IDisVoter($id)
    {
        if (! is_numeric($id)) {
            return true;
        } else {
            return false;
        }
    }

    function PersonOrVoterField($field, $id, $team_id)
    {
        if (IDisVoter($id)) {
            return Voter::where('id', $id)->first()->$field;
        }
        if (IDisPerson($id)) {
            return Person::where('id', $id)->where('team_id', $team_id)->first()->$field;
        }
    }

    function findPersonOrImportVoter($id, $team_id, $dontsave = null)
    {
        if (IDisVoter($id)) {
            if (Person::where('voter_id', $id)->where('team_id', $team_id)->exists()) {
                $theperson = Person::where('voter_id', $id)->where('team_id', $team_id)->first();
            } elseif (Person::where('old_voter_code', $id)->where('team_id', $team_id)->exists()) {
                $theperson = Person::where('old_voter_code', $id)->where('team_id', $team_id)->first();
            } else {
                $theperson = new Person;
                $theperson->team_id = $team_id;

                $thevoter = Voter::find($id);

                if (! $thevoter) {
                    $thevoter = VoterMaster::find($id);
                }

                if (! $thevoter) {
                    // If voter id not found, returns null
                    return null;
                }

                $theperson->full_name = titleCase($thevoter->full_name);
                $theperson->full_name_middle = titleCase($thevoter->full_name_middle);
                $theperson->household_id = $thevoter->household_id;
                $theperson->mass_gis_id = $thevoter->mass_gis_id;
                $theperson->full_address = $thevoter->full_address;

                $theperson->voter_id = $thevoter->id;
                $theperson->first_name = titleCase($thevoter->first_name);
                $theperson->middle_name = titleCase($thevoter->middle_name);
                $theperson->last_name = titleCase($thevoter->last_name);

                $theperson->address_number = ucwords(strtolower($thevoter->address_number));
                $theperson->address_fraction = ucwords(strtolower($thevoter->address_fraction));
                $theperson->address_street = ucwords(strtolower($thevoter->address_street));
                $theperson->address_city = ucwords(strtolower($thevoter->address_city));
                $theperson->address_state = strtoupper($thevoter->address_state);
                $theperson->address_apt = ucwords(strtolower($thevoter->address_apt));
                $theperson->address_zip = $thevoter->address_zip;

                if (abs((int) $thevoter->address_lat) > 0) {
                    $theperson->address_lat = $thevoter->address_lat;
                }
                if (abs((int) $thevoter->address_long) > 0) {
                    $theperson->address_long = $thevoter->address_long;
                }

                $theperson->mailing_info = $thevoter->mailing_info;
                $theperson->business_info = $thevoter->business_info;

                // Put emails and phone in the right places
                $emails = $thevoter->emails;
                if ($emails) {
                    if (is_array($emails) && count($emails) > 0) {
                        $theperson->primary_email = $emails[0];
                        if (count($emails) > 1) {
                            $other_emails = [];
                            foreach (array_slice($emails, 1) as $value) {
                                $other_emails[] = [$value, null];
                            }
                            $theperson->other_emails = $other_emails;
                        }
                    }
                }
                $theperson->primary_phone = $thevoter->cell_phone;
                if ($thevoter->home_phone) {
                    $theperson->other_phones = [[$thevoter->home_phone, 'Home']];
                }

                $theperson->gender = $thevoter->gender;
                $theperson->party = $thevoter->party;
                $theperson->dob = $thevoter->dob;

                // Political districts
                $theperson->governor_district = $thevoter->governor_district;
                $theperson->congress_district = $thevoter->congress_district;
                $theperson->senate_district = $thevoter->senate_district;
                $theperson->house_district = $thevoter->house_district;

                $theperson->county_code = $thevoter->county_code;
                $theperson->ward = $thevoter->ward;
                $theperson->precinct = $thevoter->precinct;
                $theperson->city_code = $thevoter->city_code;

                if (is_numeric($thevoter->voterID)) {
                    $theperson->old_cc_id = $thevoter->voterID;
                }

                if (!$dontsave) {
                    $theperson->save();
                }
            }
        }

        if (IDisPerson($id)) {
            $theperson = Person::find($id);
        }

        return $theperson;
    }

    function findParticipantOrImportVoter($id, $team_id)
    {

        // dd(Auth::user()->team, $team_id, Auth::user()->team->id);
        if (IDisVoter($id)) {
            if (Participant::where('voter_id', $id)->where('team_id', $team_id)->exists()) {
                $participant = Participant::where('voter_id', $id)->where('team_id', $team_id)->first();
            } else {
                $participant = new Participant;
                $participant->team_id = $team_id;
                $participant->user_id = Auth::user()->id;

                $thevoter = Voter::find($id);

                if (! $thevoter) {
                    $thevoter = VoterMaster::find($id);
                }

                if (! $thevoter) {
                    // If voter id not found, returns null
                    return null;
                }

                $participant->voter_id = $thevoter->id;
                $participant->full_name = titleCase($thevoter->full_name);
                $participant->first_name = titleCase($thevoter->first_name);
                $participant->middle_name = titleCase($thevoter->middle_name);
                $participant->last_name = titleCase($thevoter->last_name);
                $participant->address_number = ucwords(strtolower($thevoter->address_number));
                $participant->address_fraction = ucwords(strtolower($thevoter->address_fraction));
                $participant->address_street = ucwords(strtolower($thevoter->address_street));
                $participant->address_city = ucwords(strtolower($thevoter->address_city));
                $participant->address_state = strtoupper($thevoter->address_state);
                $participant->address_apt = ucwords(strtolower($thevoter->address_apt));
                $participant->address_zip = $thevoter->address_zip;

                $participant->ward = $thevoter->ward;
                $participant->precinct = $thevoter->precinct;
                $participant->gender = $thevoter->gender;
                $participant->party = $thevoter->party;

                $participant->congress_district = $thevoter->congress_district;
                $participant->senate_district = $thevoter->senate_district;
                $participant->house_district = $thevoter->house_district;

                if ($person = $thevoter->is_person) {
                    $participant->crossteam_person_id = $person->id;
                    if ($person->primary_phone != $thevoter->phone) {
                        $participant->primary_phone = $person->primary_phone;
                    }
                    if ($person->primary_email != $thevoter->email) {
                        $participant->primary_email = $person->primary_email;
                    }
                }

                $participant->save();
                updateParticipants();
            }
        } else {
            $participant = Participant::find($id);
        }

        return $participant;
    }

    function titleCase($string)
    {
        // Based on https://www.media-division.com/correct-name-capitalization-in-php/
        $word_splitters = [' ', '-', "O'", "L'", "D'", 'St.', 'Mc'];
        $lowercase_exceptions = ['the', 'van', 'den', 'von', 'und', 'der', 'de', 'da', 'of', 'and', "l'", "d'"];
        $uppercase_exceptions = ['III', 'IV', 'VI', 'VII', 'VIII', 'IX'];
        $string = strtolower($string);

        //I added this Irish thing
        $substitutions = [

            ['okeefe', "o'keefe"],
            ['oconnor', "o'connor"],
            ['obrien', "o'brien"],
            ['osullivan', "o'sullivan"],
            ['oreilly', "o'reilly"],
            ['ocarroll', "o'carroll"],
            ['ofarrell', "o'farrell"],
            ['ocallaghan', "o'callaghan"],
            ['omahony', "o'mahony"],
            ['oshea', "o'shea"],
            ['oleary', "o'leary"],
            ['odoherty', "o'doherty"],
            ['odonnell', "o'donnell"],
            ['oconnell', "o'connell"],
            ['orourke', "o'rourke"],
            ['odwyer', "o'dwyer"],
        ];
        foreach ($substitutions as $key => $value) {
            $string = str_replace($substitutions[$key][0], $substitutions[$key][1], $string);
        }

        foreach ($word_splitters as $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = [];
            foreach ($words as $word) {
                if (in_array(strtoupper($word), $uppercase_exceptions)) {
                    $word = strtoupper($word);
                } elseif (! in_array($word, $lowercase_exceptions)) {
                    $word = ucfirst($word);
                }

                $newwords[] = $word;
            }

            if (in_array(strtolower($delimiter), $lowercase_exceptions)) {
                $delimiter = strtolower($delimiter);
            }

            $string = implode($delimiter, $newwords);
        }

        return $string;
    }
    function dateIsClean($date)
    {
        if (! $date) {
            return false;
        }
        if (Str::startsWith($date, '0000-00-00')) {
            return false;
        }

        try {
            $carbondate = Carbon::parse($date);
        } catch (\Exception $e) {
            return false;
        }
        $datearr = explode('-', $date);
        $year = (int) $datearr[0];
        $month = (int) $datearr[1];
        $day = (int) $datearr[2];
        if ($day < 1) {
            $day = 1;
        }
        if ($month < 1) {
            $month = 1;
        }
        $tempcarbondate = Carbon::parse("$year-$month-$day");
        if ($tempcarbondate > Carbon::parse('1900-01-01')) {
            return $carbondate->format('Y-m-d h:i:s');
        }

        return false;
    }

    // https://www.php.net/manual/en/function.array-key-last.php
    if (! function_exists('array_key_last')) {
        /**
         * Polyfill for array_key_last() function added in PHP 7.3.
         *
         * Get the last key of the given array without affecting
         * the internal array pointer.
         *
         * @param array $array An array
         *
         * @return mixed The last key of array if the array is not empty; NULL otherwise.
         */
        function array_key_last($array)
        {
            $key = null;

            if (is_array($array)) {
                end($array);
                $key = key($array);
            }

            return $key;
        }
    }
    function logTime($arr, $note)
    {
        if (! isset($arr['last'])) {
            $length = 0;
        } else {
            $length = microtime(true) - $arr['last'];
        }

        $arr['log'][$note] = $length;
        $arr['last'] = microtime(true);
        if (! isset($arr['max'])) {
            $arr['max'] = $length;
        } else {
            $arr['max'] = max($arr['max'], $length);
        }
        if (! isset($arr['total'])) {
            $arr['total'] = $length;
        } else {
            $arr['total'] = $arr['total'] + $length;
        }

        return $arr;
    }
    function remove_outliers($dataset, $magnitude = 1) 
    {

        $count = count($dataset);
        $mean = array_sum($dataset) / $count; // Calculate the mean
        $deviation = sqrt(array_sum(array_map("sd_square", $dataset, array_fill(0, $count, $mean))) / $count) * $magnitude; // Calculate standard deviation and times by magnitude

        return array_filter($dataset, function($x) use ($mean, $deviation) { return ($x <= $mean + $deviation && $x >= $mean - $deviation); }); // Return filtered array of values that lie within $mean +- $deviation.
    }

    function sd_square($x, $mean) 
    {
        return pow($x - $mean, 2);
    }
