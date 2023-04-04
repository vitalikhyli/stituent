<?php

namespace App\Console\Commands;

use App\GroupPerson;
use App\Participant;
use App\Person;
use App\Team;
use App\UserUpload;
use App\UserUploadData;
use App\Voter;
use App\VoterMaster;
use Auth;
use Illuminate\Console\Command;

class UserUploadIntegrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:user_upload_integrate {--upload_id=} {--team_id=} {--user_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integrates user data with CF';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function valueFromUserColumn($upload, $userdata, $column, $irish_case = null)
    {
        $field_index = array_search($column, $upload->columns);
        $array = $userdata->data;
        $new = trim($array[$field_index]);
        if ($irish_case) {
            $new = titleCase($new);
        }

        return $new;
    }

    public function getMatchedFieldIndex($upload, $db_field)
    {
        foreach ($upload->column_matches as $key => $match) {
            if ($match['db'] == $db_field) {
                return array_search($match['user'], $upload->columns);
            }
        }

        return null;
    }

    public function createParticipant($upload, $team_id, $user_id, $userdata)
    {
        $participant = null;

        if ($userdata->voter_id) {
            $participant = $this->importVoterToNew(\App\Participant::class, $userdata->voter_id, $team_id, $user_id);
        }

        if (! $participant) {
            $participant = new Participant;
            $participant->team_id = $team_id;
            $participant->user_id = $user_id;
            $participant->voter_id = ($userdata->voter_id) ? $userdata->voter_id : null;
            $participant->save();
        }

        if ($participant) {
            $participant->upload_id = $upload->id;
            $participant->save();
        }

        return $participant;
    }

    public function createPerson($upload, $team_id, $user_id, $userdata)
    {
        $person = null;

        if ($userdata->voter_id) {
            $person = $this->importVoterToNew(\App\Person::class, $userdata->voter_id, $team_id, $user_id);
        }

        if (! $person) {
            $person = new Person;
            $person->team_id = $team_id;
            $person->created_by = $user_id;
            $person->voter_id = ($userdata->voter_id) ? $userdata->voter_id : null;
        }

        if ($person) {
            $person->upload_id = $upload->id;
            $person->save();
        }

        return $person;
    }

    public function importVoterToNew($model_type, $voter_id, $team_id, $user_id)
    {
        $thevoter = VoterMaster::find($voter_id);

        if (! $thevoter) {
            return null;
        }

        $model = new $model_type;

        $model->full_name = titleCase($thevoter->full_name);
        $model->full_address = $thevoter->full_address;

        $model->voter_id = $thevoter->id;
        $model->first_name = titleCase($thevoter->first_name);
        $model->middle_name = titleCase($thevoter->middle_name);
        $model->last_name = titleCase($thevoter->last_name);

        $model->address_number = ucwords(strtolower($thevoter->address_number));
        $model->address_fraction = ucwords(strtolower($thevoter->address_fraction));
        $model->address_street = ucwords(strtolower($thevoter->address_street));
        $model->address_city = ucwords(strtolower($thevoter->address_city));
        $model->address_state = strtoupper($thevoter->address_state);
        $model->address_apt = ucwords(strtolower($thevoter->address_apt));
        $model->address_zip = $thevoter->address_zip;

        // Put emails and phone in the right places
        $emails = $thevoter->emails;
        if ($emails) {
            if (count($emails) > 0) {
                $model->primary_email = $emails[0];
                if (count($emails) > 1) {
                    $other_emails = [];
                    foreach (array_slice($emails, 1) as $value) {
                        $other_emails[] = [$value, null];
                    }
                    $model->other_emails = $other_emails;
                }
            }
        }
        $model->primary_phone = $thevoter->cell_phone;
        if ($thevoter->home_phone) {
            $model->other_phones = [[$thevoter->home_phone, 'Home']];
        }

        $model->gender = $thevoter->gender;
        $model->party = $thevoter->party;

        // Political districts

        $model->congress_district = $thevoter->congress_district;
        $model->senate_district = $thevoter->senate_district;
        $model->house_district = $thevoter->house_district;

        $model->ward = $thevoter->ward;
        $model->precinct = $thevoter->precinct;
        $model->city_code = $thevoter->city_code;

        if ($model_type == \App\Person::class) {
            $model->household_id = $thevoter->household_id;
            $model->full_name_middle = titleCase($thevoter->full_name_middle);
            $model->mass_gis_id = $thevoter->mass_gis_id;
            $model->mailing_info = $thevoter->mailing_info;
            $model->business_info = $thevoter->business_info;
            $model->old_cc_id = $thevoter->voterID;
            $model->county_code = $thevoter->county_code;
            $model->governor_district = $thevoter->governor_district;
            if (abs((int) $thevoter->address_lat) > 0) {
                $model->address_lat = $thevoter->address_lat;
            }
            if (abs((int) $thevoter->address_long) > 0) {
                $model->address_long = $thevoter->address_long;
            }
            $model->dob = $thevoter->dob;
        }
        if ($model_type == \App\Participant::class) {
            $model->user_id = $user_id;
        }
        $model->team_id = $team_id;
        $model->save();

        return $model;
    }

    public function conditionLogic($if_rule, $if_qual, $field_value)
    {
        $tof = (! $if_rule) ? true : false; //No Condition is given, then always True

        if (! $tof) {
            if ($if_rule == 'blank') {
                $tof = (! $field_value) ? true : false;
            }

            if ($if_rule == 'not-blank') {
                $tof = ($field_value) ? true : false;
            }

            if ($if_rule == 'gt' && is_numeric($field_value) && is_numeric($if_qual)) {
                $tof = ($field_value > $if_qual) ? true : false;
            }

            if ($if_rule == 'lt' && is_numeric($field_value) && is_numeric($if_qual)) {
                $tof = ($field_value < $if_qual) ? true : false;
            }

            if ($if_rule == 'eq') {
                $tof = (strtolower($field_value) == strtolower($if_qual)) ? true : false;
            }

            if ($if_rule == 'not-eq') {
                $tof = (strtolower($field_value) != strtolower($if_qual)) ? true : false;
            }
        }

        return $tof;
    }

    public function checkVoterIDPrefix($data)
    {
        return strtoupper((substr($data, 0, 3) != 'MA_') ? 'MA_'.$data : $data);
    }

    public function handle()
    {
        $sleep = 0; //250000;

        $upload_id = $this->option('upload_id');
        $team_id = $this->option('team_id');
        $user_id = $this->option('user_id');

        Auth::loginUsingId($user_id);

        $team = Team::find($team_id); //Auth is problematic because cached

        session()->put('team_table', Team::find($team_id)->db_slice);

        $upload = UserUpload::find($upload_id);

        //////////////////////////////// Loop ////////////////////////////////

        $at_at_time = 100;
        $remaining = true;

        while ($remaining == true) {
            $voters_userdata = UserUploadData::where('team_id', $team_id)
                                            ->where('upload_id', $upload->id)
                                            ->whereNull('integrated_at')
                                            ->take($at_at_time)
                                            ->get();

            if ($voters_userdata->count() <= 0) {
                $remaining = false;
            }

            foreach ($voters_userdata as $userdata) {
                $voter = null;
                $person = null;
                $participant = null;

                ////////////////////////// INTEGRATION //////////////////////////

                $voter = Voter::find($userdata->voter_id);
                $person = Person::find($userdata->person_id);
                $participant = Participant::find($userdata->participant_id);

                $create_new = false;

                switch ($upload->new_rules) {

                    case 'always':
                        $create_new = true;
                        break;

                    case 'voters_only':
                        if ($voter) {
                            $create_new = true;
                        }
                        break;

                    case 'if-email':
                        $matches = $userdata->data;
                        $index = $this->getMatchedFieldIndex($upload, 'primary_email');
                        $primary_email = ($matches[$index]) ? $matches[$index] : null;
                        if ($primary_email) {
                            $create_new = true;
                        }
                        break;

                    // case 'if-email-or-voter-email':
                    //     $matches = $userdata->data;
                    //     $index   = $this->getMatchedFieldIndex($upload, 'primary_email');
                    //     $primary_email  = $matches[$index];

                    //     if ($voter) {
                    //         if ($primary_email || $voter->emails) $create_new = true;
                    //     }
                    //     if (!$voter) {
                    //         if ($primary_email) $create_new = true;
                    //     }
                    //     break;

                }

                if ($create_new) {
                    if (($team->app_type == 'campaign') && ! $participant) {
                        $participant = $this->createParticipant($upload, $team_id, $user_id, $userdata);
                    }

                    if (($team->app_type != 'campaign') && ! $person) {
                        $person = $this->createPerson($upload, $team_id, $user_id, $userdata);
                    }
                }

                /////////////////////////// VOTER ///////////////////////////

                // Voter File should not be updated

                //////////////////////// PARTICIPANT ////////////////////////

                if ($participant) {
                    foreach ($upload->column_map as $column => $column_rules) {
                        foreach ($column_rules as $rule_id => $rule) {

                            // Update & Replace

                            if ($rule['action'] == 'replace') {
                                $db_field = $rule['qual'];
                                if ($db_field) {
                                    $new = $this->valueFromUserColumn($upload, $userdata, $column, $irish_case = true);

                                    if ($db_field == 'voter_id') {
                                        $new = $this->checkVoterIDPrefix($new);
                                    }

                                    if ($new) {
                                        $participant->$db_field = $new;
                                    }
                                }
                            }

                            // Update if Blank

                            if ($rule['action'] == 'if-empty') {
                                $db_field = $rule['qual'];
                                if ($db_field) {
                                    if (empty(trim($participant->$db_field))) {
                                        $new = $this->valueFromUserColumn($upload, $userdata, $column, $irish_case = true);

                                        if ($db_field == 'voter_id') {
                                            $new = $this->checkVoterIDPrefix($new);
                                        }

                                        if ($new) {
                                            $participant->$db_field = $new;
                                        }
                                    }
                                }
                            }

                            // Add Emails

                            if ($rule['action'] == 'email-add') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new) {
                                    $note = $rule['qual'];
                                    $other_emails = $participant->other_emails;
                                    $other_emails[] = [$new, $note];
                                    $participant->other_emails = $other_emails;
                                }
                            }

                            if ($rule['action'] == 'email-primary') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $participant->primary_email)) {
                                    if ($participant->primary_email) {
                                        // Shift this over to other_ array
                                        $other_emails = $participant->other_emails;
                                        $other_emails[] = [$participant->primary_email, 'Old Primary'];
                                        $participant->other_emails = $other_emails;
                                    }
                                    $participant->primary_email = $new;
                                }
                            }

                            if ($rule['action'] == 'email-work') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $participant->work_email)) {
                                    if ($participant->work_email) {
                                        // Shift this over to other_ array
                                        $other_emails = $participant->other_emails;
                                        $other_emails[] = [$participant->work_email, 'Old Work'];
                                        $participant->other_emails = $other_emails;
                                    }
                                    $participant->work_email = $new;
                                }
                            }

                            // Add Phones

                            if ($rule['action'] == 'phone-add') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new) {
                                    $note = $rule['qual'];
                                    $other_phones = $participant->other_phones;
                                    $other_phones[] = [$new, $note];
                                    $participant->other_phones = $other_phones;
                                }
                            }

                            if ($rule['action'] == 'phone-primary') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $participant->primary_phone)) {
                                    if ($participant->primary_phone) {
                                        // Shift this over to other_ array
                                        $other_phones = $participant->other_phones;
                                        $other_phones[] = [$participant->primary_phone, 'Old Primary'];
                                        $participant->other_phones = $other_phones;
                                    }
                                    $participant->primary_phone = $new;
                                }
                            }

                            if ($rule['action'] == 'phone-work') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $participant->work_phone)) {
                                    if ($participant->work_phone) {
                                        // Shift this over to other_ array
                                        $other_phones = $participant->other_phones;
                                        $other_phones[] = [$participant->work_phone, 'Old Work'];
                                        $participant->other_phones = $other_phones;
                                    }
                                    $participant->work_phone = $new;
                                }
                            }

                            // Tags

                            if ($rule['action'] == 'tag') {
                                $tag_id = $rule['qual'];

                                $field_value = $this->valueFromUserColumn($upload, $userdata, $column);

                                $tof = $this->conditionLogic($rule['if'], $rule['if-qual'], $field_value);

                                // ADD: Authorization Policy Check if Tag Belongs to Team

                                if ($tag_id && $tof) {
                                    $data[$tag_id] = ['team_id'  => $team_id,
                                                      'user_id'  => $user_id,
                                                      'voter_id' => $participant->voter_id, ];
                                    $participant->tags()->attach($data);
                                }
                            }
                        }
                    }

                    $participant->save();
                    $userdata->participant_id = $participant->id;
                }

                ////////////////////////// PERSON //////////////////////////

                if ($person) {
                    foreach ($upload->column_map as $column => $column_rules) {
                        foreach ($column_rules as $rule_id => $rule) {

                            // Update & Replace

                            //dd($rule, $column);

                            if ($rule['action'] == 'replace') {
                                $db_field = $rule['qual'];

                                if ($db_field) {
                                    $new = $this->valueFromUserColumn($upload, $userdata, $column, $irish_case = true);

                                    if ($db_field == 'voter_id') {
                                        $new = $this->checkVoterIDPrefix($new);
                                    }

                                    if ($new) {
                                        $person->$db_field = $new;
                                    }
                                }
                            }

                            // Update if Blank
                            // echo $column." ".$rule['action']." ".$rule['qual']."\r\n";
                            if ($rule['action'] == 'if-empty') {
                                $db_field = $rule['qual'];
                                // echo 'Field: '.$db_field."\r\n";
                                if ($db_field) {
                                    if (empty(trim($person->$db_field))) {
                                        $new = $this->valueFromUserColumn($upload, $userdata, $column, $irish_case = true);

                                        if ($db_field == 'voter_id') {
                                            $new = $this->checkVoterIDPrefix($new);
                                        }

                                        if ($new) {
                                            $person->$db_field = $new;
                                        }
                                    }
                                }
                            }

                            // Add Emails

                            if ($rule['action'] == 'email-add') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new) {
                                    $note = $rule['qual'];
                                    $other_emails = $person->other_emails;
                                    $other_emails[] = [$new, $note];
                                    $person->other_emails = $other_emails;
                                }
                            }

                            if ($rule['action'] == 'email-primary') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $person->primary_email)) {
                                    if ($person->primary_email) {
                                        // Shift this over to other_ array
                                        $other_emails = $person->other_emails;
                                        $other_emails[] = [$person->primary_email, 'Old Primary'];
                                        $person->other_emails = $other_emails;
                                    }
                                    $person->primary_email = $new;
                                }
                            }

                            if ($rule['action'] == 'email-work') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $person->work_email)) {
                                    if ($person->work_email) {
                                        // Shift this over to other_ array
                                        $other_emails = $person->other_emails;
                                        $other_emails[] = [$person->work_email, 'Old Work'];
                                        $person->other_emails = $other_emails;
                                    }
                                    $person->work_email = $new;
                                }
                            }

                            // Add Phones

                            if ($rule['action'] == 'phone-add') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new) {
                                    $note = $rule['qual'];
                                    $other_phones = $person->other_phones;
                                    $other_phones[] = [$new, $note];
                                    $person->other_phones = $other_phones;
                                }
                            }

                            if ($rule['action'] == 'phone-primary') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $person->primary_phone)) {
                                    if ($person->primary_phone) {
                                        // Shift this over to other_ array
                                        $other_phones = $person->other_phones;
                                        $other_phones[] = [$person->primary_phone, 'Old Primary'];
                                        $person->other_phones = $other_phones;
                                    }
                                    $person->primary_phone = $new;
                                }
                            }

                            if ($rule['action'] == 'phone-work') {
                                $new = $this->valueFromUserColumn($upload, $userdata, $column);
                                if ($new && ($new != $person->work_phone)) {
                                    if ($person->work_phone) {
                                        // Shift this over to other_ array
                                        $other_phones = $person->other_phones;
                                        $other_phones[] = [$person->work_phone, 'Old Work'];
                                        $person->other_phones = $other_phones;
                                    }
                                    $person->work_phone = $new;
                                }
                            }

                            // Groups

                            if ($rule['action'] == 'group') {
                                $group_id = $rule['qual'];

                                $field_value = $this->valueFromUserColumn($upload, $userdata, $column);

                                $tof = $this->conditionLogic($rule['if'], $rule['if-qual'], $field_value);

                                // ADD: Authorization Policy Check if Group Belongs to Team

                                if ($group_id && $tof) {
                                    $pivot = GroupPerson::where('team_id', $team_id)
                                                        ->where('group_id', $group_id)
                                                        ->where('person_id', $person->id)
                                                        ->first();

                                    if (! $pivot) {
                                        $pivot = new GroupPerson;
                                    }

                                    $pivot->team_id = $team_id;
                                    $pivot->group_id = $group_id;
                                    $pivot->person_id = $person->id;
                                    $pivot->save();
                                }
                            }
                        }
                    }

                    $person->save();
                    $userdata->person_id = $person->id;
                }

                ////////////////////////// FINISH //////////////////////////

                $userdata->integrated_at = now();
                $userdata->save();
                $upload->integrated_count++;
            }

            $upload->save();
            usleep($sleep);
        }
    }
}
