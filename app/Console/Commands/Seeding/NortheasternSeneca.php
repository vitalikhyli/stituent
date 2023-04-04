<?php

namespace App\Console\Commands\Seeding;

use App\CasePerson;
use App\Contact;
use App\Entity;
use App\EntityPerson;
use App\Models\CC\NEUAddress;
use App\Models\CC\NEUCasework;
use App\Models\CC\NEUIssueAction;
use App\Models\CC\NEUPerson;
use App\Models\CC\NEUPhoneEmail;
use App\Municipality;
use App\Person;
use App\Team;
use App\TeamUser;
use App\User;
use App\Voter;
use App\VoterMaster;
use App\WorkCase;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;

class NortheasternSeneca extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_seneca';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull old NU data from remote';

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
    public function handle()
    {

        // dd(NEUCasework::where('senecaID',89751382)->first());  Shauneen McKinlay
        // dd(NEUIssueAction::where('caseworkid',610)->first());

        $team = Team::where('old_cc_id', 409)->first();
        $user = User::where('username', 'disberg')->first();
        session()->put('team_table', $team->db_slice);
        $start = microtime(true);

        // Get Seneca IDs that are part of other tables. Omit orphans. I HATE ORPHANS.

        $list_of_ids = NEUCasework::all()->pluck('senecaID')->unique()->toArray();

        $this->info('People');
        $this->cycleThroughRelevantPeople($list_of_ids, $team);

        $this->showElapsed($start);
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  PEOPLE
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  "senecaID" => 92010042      <------------------- Primary Key
    //  "stateID" => "06YCN0891000" <------------------- VoterFile
    //  "voterID" => 6548165        <------------------- OLD_CC_ID ??????
    //  "firstname" => "CALVIN"
    //  "lastname" => "YOUNG"
    //  "middlename" => ""
    //  "suffix" => "II"
    //  "dob" => "06/08/1991"
    //  "occupation" => ""          business_info
    //  "employer" => ""            business_info
    //  "organization" => ""        <------------------- How to handle?
    //  "ssn" => 0                  private
    //  "linkedto" => ""            <------------------- WHAT IS THIS?
    //  "gender" => "M"
    //  "party" => "D"
    //  "id" => 339242              <------------------- WHAT IS THIS?
    //
    // COUNT =  372,773
    // linkedto not equal to "" = 3
    // --------------------------------------------------------------------------------------//

    public function cycleThroughRelevantPeople($list_of_ids, $team)
    {
        $continue = true;
        $increment = 5;
        $count = 0;
        $limit = null; // For testing if needed
        $expected = count($list_of_ids);

        $organization_list = [];
        $linkedto_list = [];

        while ($continue == true) {
            $query = NEUPerson::select('senecaID', 'stateID', 'voterID', 'firstname', 'lastname', 'middlename', 'suffix', 'dob', 'occupation', 'employer', 'organization', 'linkedto', 'gender', 'party', 'id')->whereIn('senecaID', $list_of_ids)->skip($count)->take($increment)->get();

            if ($limit && $count > $limit) {
                $continue = false;
            }
            if ($query->count() > 0) {
                foreach ($query as $record) {

                    // In case command has been run more than once -- do not repeat same people
                    $person = Person::where('old_voter_code', 'Seneca_'.$record->senecaID)->first();

                    if (! $person) {
                        $existing_id = $this->PersonExistsByVoterID($record->stateID, $team);

                        if ($existing_id) {
                            $person = Person::find($existing_id);
                            echo 'Found Existing Person in DB: '.$person->full_name."\r\n";
                        } else {
                            $person = Person::where('team_id', $team->id)
                                            ->where('old_voter_code', 'Seneca_'.$record->SenecaID)
                                            ->first();
                        }

                        if (! $person) {
                            $person = new Person;
                        }
                    }

                    $person->team_id = $team->id;
                    $person->old_voter_code = 'Seneca_'.$record->senecaID;
                    $person->voter_id = ($record->stateID) ? 'MA_'.$record->stateID : null;

                    // if (!env('LOCAL_MACHINE') != 'Slothe') {
                    //     $person->private        = $record->ssn;
                    // }

                    $person->first_name = titleCase($record->firstname);
                    $person->last_name = titleCase($record->lastname);
                    $person->middle_name = titleCase($record->middlename);
                    $person->suffix_name = titleCase($record->suffix);

                    $person->dob = ($record->dob) ? Carbon::parse($record->dob)->toDateString() : null;

                    $person->gender = $record->gender;
                    $person->party = $record->party;

                    $person->business_info = ['name' => $record->employer,
                                                   'occupation' => $record->occupation, ];

                    // Additional information

                    $person->old_private = ['seneca' => ['senecaID'      => $record->senecaID,
                                                                'id'            => $record->id,
                                                                'voterID'       => $record->voterID,
                                                                'linkedto'      => $record->linkedto,
                                                                'organization'  => $record->organization,
                                                                ],
                                                  ];

                    $person->save();

                    echo "Address     \r";
                    $this->lookupAddressAndSave($person);

                    // Commenting this out because bad data
                    // echo "Phone Email     \r";
                    // $this->lookupPhoneEmailAndSave($person);

                    echo "Casework     \r";
                    $this->lookupCaseworkAndSave($person, $team);

                    if ($record->organization) {
                        //$this->addAndOrLinkOrganization($record->senecaID, $record->organization, $team);
                    }

                    if ($record->linkedto) {
                        //$this->addAndOrLinkOrganization($record->senecaID, $record->linkedto, $team);
                    }
                }

                $count += $increment;

                $percentage = $count / $expected;
                $bar_length = 40;
                $bar = str_repeat('*', round($percentage * $bar_length, 0)).
                        str_repeat('-', $bar_length - round($percentage * $bar_length, 0));
                $this->info('Got record # '.$count.' of '.$expected.' '.$bar.' ('.round($percentage * 100, 0).'%)');
            } else {
                $continue = false;
            }
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  ADDRESS
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    //
    // "senecaID" => 1
    // "description" => ""
    // "header" => ""
    // "street" => "1234 MAIN ST"
    // "apartment" => ""
    // "city" => "CAMBRIDGE"
    // "state" => "MA"
    // "zip" => 2478
    // "ward" => 0
    // "precinct" => 0
    // "id" => 1
    //
    // COUNT = 372,661
    // --------------------------------------------------------------------------------------//

    public function lookupAddressAndSave($person)
    {
        $record = NEUAddress::where('senecaID', $this->SenecaIDFromOldVoterCode($person->old_voter_code))
                            ->first();

        if (! $record) {
            return;
        }

        $number = null;
        $street = null;
        $address_arr = explode(' ', trim($record->street), 2);

        if (count($address_arr) > 0) {
            $first = $address_arr[0];
            if (preg_match('/\\d/', $first) > 0) {
                $number = $first;
                if (count($address_arr) > 1) {
                    $street = $address_arr[1];
                }
            } else {
                $street = $first;
            }
        }

        $person->address_number = $number;
        $person->address_street = titleCase($street);
        $person->address_city = titleCase($record->city);
        $person->address_state = strtoupper($record->state);
        $person->address_zip = str_pad($record->zip, 5, '0', STR_PAD_LEFT);
        $person->ward = $record->ward;
        $person->precinct = $record->precinct;

        $city = Municipality::where('name', $record->city)->first();

        if ($city) {
            $person->city_code = $city->code;
        }

        $person->save();
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  PHONE EMAIL
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    //
    // "senecaID" => 66
    // "default_number" => 2147483647
    //
    // COUNT = 251,011
    // COUNT = 250,992 = default_number is not ""
    // --------------------------------------------------------------------------------------//

    public function lookupPhoneEmailAndSave($person)
    {

        // Appears to be only phones no emails

        $record = NEUPhoneEmail::where('senecaID', $this->SenecaIDFromOldVoterCode($person->old_voter_code))->first();

        if (! $record) {
            return;
        }

        $person->primary_phone = $record->default_number;
        $person->save();
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  CASEWORK
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    //
    // "senecaID" => 1
    // "caseworkid" => 1
    // "casework_type" => "Athletics"
    // "casework_subtype" => "Ticket Request List"
    // "casework_status" => "Closed"
    // "contact_initiated" => "Mail"
    // "dateofcontact" => "05/14/2014"
    // "createdby" => "David Isberg"
    // "assignedto" => "David Isberg"
    // "duedate" => "2014-05-14 00:00:00"
    // "resolved" => "Y"
    //
    // COUNT =  1,038
    // --------------------------------------------------------------------------------------//
    //
    // $model = NEUCasework::count();
    // dd($model);

    public function lookupCaseworkAndSave($person, $team)
    {
        $query = NEUCasework::where('senecaID', $this->SenecaIDFromOldVoterCode($person->old_voter_code))->get();

        if (! $query->first()) {
            return;
        }

        foreach ($query as $record) {
            $case = WorkCase::where('team_id', $team->id)
                            ->where('old_cc_id', $record->caseworkid)
                            ->first();
            if (! $case) {
                $case = new WorkCase;
            }
            $case->team_id = $team->id;
            $case->user_id = $this->senecaUserCreateOrGetID($record->assignedto, $person->team_id);

            $type_combined = ($record->casework_type) ? $record->casework_type : null;
            if ($record->casework_subtype) {
                $type_combined .= ' - '.$record->casework_subtype;
            }

            $case->type = $type_combined;

            // There were 17 records with blank dateofcontact fields
            $the_date = $record->dateofcontact;
            if (! $the_date) {
                $case->date = null;
                $case->created_at = null;
            } else {
                $case->date = Carbon::parse($the_date)->toDateString();
                $case->created_at = $case->date;
            }

            $the_notes = null;
            if ($record->contact_initiated) {
                $the_notes .= 'Initiated: '.$record->contact_initiated;
            }
            if ($record->duedate && $record->contact_initiated) {
                $the_notes .= "\n";
            }
            if ($record->duedate) {
                $the_notes .= 'Due: '.Carbon::parse($record->duedate)->toDateString();
            }
            $case->notes = $the_notes;

            $status = $record->casework_status;
            // dd(NEUCasework::select('casework_status')->groupBy('casework_status')->pluck('casework_status')->toArray());
            // THESE ARE THE STATUS OPTIONS IN THE OLD SYSTEM:
            //   0 => ""
            //   1 => "Active"
            //   2 => "Closed"
            //   3 => "Open"
            //   4 => "Pledge Reviewed"
            if ($record->casework_status == 'Closed') {
                $status = 'resolved';
            }
            if ($record->casework_status == 'Open') {
                $status = 'open';
            }
            if ($record->casework_status == 'Active') {
                $status = 'open';
            }
            if ($record->casework_status == 'Pledge Reviewed') {
                $status = 'held';
            }     // Correct????
            if ($record->casework_status == '') {
                $status = 'open';
            }     // Correct????
            if ($record->resolved == 'Y') {
                $status = 'resolved';
            } // Should be last
            $case->status = $status;

            $case->old_cc_id = $record->caseworkid;

            $case->save();

            $person_id = $this->getPersonIDFromSenecaID($record->senecaID);
            $caseperson = CasePerson::where('team_id', $team->id)
                                    ->where('person_id', $person_id)
                                    ->where('case_id', $case->id)
                                    ->first();
            if (! $caseperson) {
                $caseperson = new CasePerson;
            }
            $caseperson->team_id = $team->id;
            $caseperson->person_id = $person_id;
            $caseperson->case_id = $case->id;
            $caseperson->created_at = $case->date;
            $caseperson->updated_at = $case->date;
            $caseperson->save();

            $person->created_at = $case->date;
            $person->save();

            $this->getCaseIssueActionsAndSave($case, $record->caseworkid);

            // If case date is blank (17 such records in all data) use first contact date
            if (! $case->date) {
                $first_contact = $case->contacts()->orderBy('date')->first();
                if ($first_contact) {
                    $case->date = Carbon::parse($first_contact->date)->toDateString();
                    $case->created_at = $case->date;
                    $case->save();
                }
            }

            // Give it the best subject possible

            $first_contact = $case->contacts()->orderBy('date', 'desc')->first();

            if ($first_contact && trim($first_contact->notes) != '') {
                $case->subject = mb_strimwidth($first_contact->notes, 0, 40, '...');
            } elseif ($record->casework_subtype) {
                $case->subject = $record->casework_subtype;
            } elseif ($record->casework_type) {
                $case->subject = $record->casework_type;
            } else {
                $case->subject = 'No subject';
            }
            $case->save();
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  ISSUE ACTION
    //
    //////////////////////////////////////////////////////////////////////////////////////////
    //
    // "caseworkid" => 1
    // "issue" => """
    //   Test\n
    //   \n
    //   [David Isberg: 5/14/2014 2:46:37 PM]
    //   """
    // "resolution" => """
    //   Test\n
    //   \n
    //   [David Isberg: 5/14/2014 2:46:37 PM]
    //   """
    //
    // COUNT =  1,038
    // --------------------------------------------------------------------------------------//

    public function getCaseIssueActionsAndSave($case, $casework_id)
    {
        $query = NEUIssueAction::where('caseworkid', $casework_id)->get();

        if (! $query->first()) {
            return;
        }

        foreach ($query as $record) {

            // Issue and Resolution sort of seem to be use the same way
            $new_list_a = [];
            $new_list_b = [];

            if ($record->issue) {
                $new_list_a = $this->processLinesIntoContacts($record->issue, $case, 'issue');
            }
            if ($record->resolution) {
                $new_list_b = $this->processLinesIntoContacts($record->resolution, $case, 'res');
            }

            $new_list = array_merge($new_list_a, $new_list_b);

            foreach ($new_list as $new) {
                $contact = Contact::where('team_id', $case->team_id)
                                  ->whereDate('date', $new['date'])
                                  ->where('case_id', $new['case_id'])
                                  ->where('notes', $new['notes'])
                                  ->first();
                if (! $contact) {
                    $contact = new Contact;
                }

                $contact->team_id = $case->team_id;
                $contact->date = $new['date'];
                $contact->created_at = $new['date'];
                $contact->user_id = $new['user_id'];
                $contact->case_id = $new['case_id'];
                $contact->notes = $new['notes'];
                $contact->source = $new['source'];
                $contact->save();
            }
        }
    }

    public function processLinesIntoContacts($text, $case, $field_type)
    {
        $lines = explode("\n", $text);

        if (! $lines) {

            // No Lines --------------------------->
            Log::stack(['seneca_errors'])->info(Carbon::now().' no lines: Case ID '.$case->id);
        } else {
            $new_list = [];
            $i = 0;
            $notes_running = null;

            foreach ($lines as $line) {
                $ex = '/'.'\[(.*):\s(.*)\]'.'/'; // [Dan Daley: 4/26/2016 11:31:58 AM]

                preg_match_all($ex, $line, $signature);

                if ($signature[0]) {

                    // This is a signature line

                    try {
                        $the_date = preg_replace('/'.'[^0-9(AM)\/\:\s]'.'/', '', $signature[2][0]);
                        $formatted_date = Carbon::parse($the_date)->toDateTimeString();
                    } catch (\Exception $e) {

                        // Can't format time ----------------------------->
                        Log::stack(['seneca_errors'])->info(Carbon::now().' cannot format time: "'.$the_date.'" Case ID '.$case->id);

                        // One error was "9/15/2011 @ 10:34:05 AM"
                        // Therefore, strip out non-date characters

                        $formatted_date = null;
                    }

                    $new_list[$i] = [
                                    'notes'     => $notes_running,
                                    'user_id'   => $this->senecaUserCreateOrGetID($signature[1][0], $case->team_id),
                                    'date'      => $formatted_date,
                                    'case_id'   => $case->id,
                                    'source'    => 'seneca-'.$field_type, // issue or res
                                    ];

                    // Whatever follows is a new contact

                    $i++;
                    $notes_running = null;
                } else {

                    //This is just another line

                    if ($line) {
                        $notes_running .= $line."\n";
                    }
                    // if ($line) $notes_running .= base64_encode($line)."\n";
                }
            }
        }

        return $new_list;
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  ORGANIZATIONS OBTAINED FROM NEU-PEOPLE
    //
    //////////////////////////////////////////////////////////////////////////////////////////

    // Note that $record->linkedto seems to be empty for all non-voter-only records

    public function addAndOrLinkOrganization($seneca_id, $org_name, $team)
    {
        $entity = Entity::where('name', $org_name)->first();

        if (! $entity) {
            $entity = new Entity;
        }

        $entity->name = $org_name;
        $entity->team_id = $team->id;
        $entity->save();

        $pivot = new EntityPerson;
        $pivot->person_id = $this->getPersonIDFromSenecaID($seneca_id);
        $pivot->entity_id = $entity->id;
        $pivot->team_id = $team->id;
        $pivot->save();

        echo '**** Created and/or linked '.$entity->name."          \r";
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  HELPER FUNCTIONS
    //
    //////////////////////////////////////////////////////////////////////////////////////////

    public function senecaUserCreateOrGetID($name, $team_id)
    {
        $default_user_id = User::whereName('David Isberg')->first()->id;

        if (! $name) {
            return $default_user_id;
        }

        $user = User::whereName($name)->first();

        if (! $user) {
            $user = new User;
            $user->name = $name;
            $user->current_team_id = $team_id;
            $user->email = str_replace(' ', '', 'placeholder.'.$name.'@stituent.com'); //Email cannot be null
            $user->save();

            $pivot = new TeamUser;
            $pivot->user_id = $user->id;
            $pivot->team_id = $team_id;
            $pivot->save();
        }

        return $user->id;
    }

    public function getPersonIDFromSenecaID($seneca_id)
    {
        $person = Person::where('old_voter_code', 'Seneca_'.$seneca_id)->first();
        if ($person) {
            return $person->id;
        } else {
            return 0;
        }
    }

    public function PersonExistsByVoterID($id, $team)
    {
        if (! $id) {
            return false;
        }
        $existing = Person::where('voter_id', $id)->where('team_id', $team->id)->first();

        return ($existing) ? $existing->id : false;
    }

    public function SenecaIDFromOldVoterCode($code)
    {
        return str_replace('Seneca_', '', $code);
    }

    public function showElapsed($start)
    {
        echo str_repeat('-', 60)."\r\n";
        $time_elapsed_secs = microtime(true) - $start;
        echo 'Elapsed: '.round($time_elapsed_secs, 2).' secs'."\r\n";
        echo str_repeat('-', 60)."\r\n";
    }

    //////////////////////////////////////////////////////////////////////////////////////////
    //
    //  TEST / EXPERIMENTAL CODE
    //
    //////////////////////////////////////////////////////////////////////////////////////////

    // public function NEUVoterFileExists($id) {
    //     if (!$id) return false;
    //     return (Voter::find('MA_'.$id)) ? true : false;
    // }

    // public function MasterVoterFileExists($id) {
    //     if (!$id) return false;
    //     return (VoterMaster::find('MA_'.$id)) ? true : false;
    // }

    // public function PersonExists($id, $team) {
    //     if (!$id) return false;
    //     return (Person::where('old_cc_id',$id)->where('team_id', $team->id)) ? true : false;
    // }

    // $q = NEUPhoneEmail::where('default_number','<>','')->count();

    // $q = NEUIssueAction::all();
    // // dd($q);
    // $max_key = 0;

    // foreach($q as $e) {
    //     $delimiter = 'M]'; // because endings: "\n[Dan Daley: 4/26/2016 11:31:58 AM]"
    //     // $contacts = explode(']',$e->issue);
    //     $contacts = explode($delimiter,$e->resolution);

    //     foreach($contacts as $key => $a) {
    //         if ($a) {
    //             echo $key.") ".trim($a).$delimiter."\r\n";
    //             if ($key > $max_key) $max_key = $key;
    //         }
    //     }
    //     echo str_repeat('-', 70)."\r\n";
    // }

    // dd($max_key); // Max resolutions is 12; Max issues is 2

    // WorkCase::where('team_id',$team->id)->delete();
    // Person::where('team_id',$team->id)->delete();
    // Contact::where('team_id',$team->id)->delete();
}
