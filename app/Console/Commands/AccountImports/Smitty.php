<?php

namespace App\Console\Commands\AccountImports;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\WorkCase;
use App\Contact;
use App\ContactPerson;
use App\CasePerson;
use App\Person;
use App\Voter;

class Smitty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:import_smitty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Smitty excel sheet to database';

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
     * @return int
     */
    public function handle()
    {

        //session(['team_table' => 'x_MA_H_68']);
        session(['team_table' => 'x_MA_STATE']);
        session(['team_state' => 'MA']);

        // =======================================> ACTIVE CASES
        $active = storage_path()."/app/csvs/smitty.csv";
        $full_data = $this->getFullData($active);

        

        foreach ($full_data as $record) {
            $case = WorkCase::where('team_id', 180)
                            ->where('date', Carbon::parse($record["Date"]))
                            ->where('subject', $record['Name'])
                            ->first();

            if (!$case) {
                $case = new WorkCase;
                $case->team_id  = 180;
                $case->date     = Carbon::parse($record["Date"]);
                $case->subject  = $record['Name'];
                $case->private  = false;
                $case->priority = null;
                $case->type     = $record['Issue'];
                $case->subtype  = null;
                $case->closing_remarks = $record['Resolution'];

                $staff = $record["Staffer Initials \nDate of Latest Update"];

                if (strpos('a'.$staff, 'RBM') > 0) {
                    $case->user_id = 1025;
                } else if (strpos('a'.$staff, 'JLM') > 0) {
                    $case->user_id = 1024;
                }
                if (!$case->user_id) {
                    $case->user_id = 384;
                }

                $case->save();
                //dd($case, $record);
            }

            if ($record['Resolution'] != 'Resolved') {
                $case->status = 'open';
            } else {
                $case->status = 'resolved';
            }

            $case->created_at = Carbon::parse($record["Date"]);

            if (!$case->notes) {
                $notes = "";
                $notes .= trim($record['Notes'])."\n";
                $notes .= trim($record['Address'])."\n";
                $notes .= trim($record['Contact Information'])."\n";
                $notes .= trim($record['Other Information'])."\n";

                $case->notes = trim($notes);
            }

            $staff = $record["Staffer Initials \nDate of Latest Update"];

            if (strpos('a'.$staff, 'RBM') > 0) {
                $case->user_id = 1025;
            } else if (strpos('a'.$staff, 'JLM') > 0) {
                $case->user_id = 1024;
            }
            if (!$case->user_id) {
                $case->user_id = 384;
            }
            
            $case->save();
            //dd($case);

            $method = "";
            if ($record["Method of Inquiry"]) {
                if (strpos('a'.$record["Method of Inquiry"], 'Phone') > 0) {
                    $method = 'Phone';
                } else if (strpos('a'.$record["Method of Inquiry"], 'Email') > 0) {
                    $method = 'Email';
                }
            }

            $contact = Contact::where('team_id', 180)
                            ->where('user_id', $case->user_id)
                            ->where('date', Carbon::parse($record["Date"]))
                            ->where('subject', $record['Name'])
                            ->first();
            if (!$contact) {
                
                $contact = new Contact;
                $contact->team_id = 180;
                $contact->user_id = $case->user_id;
                $contact->case_id = $case->id;
                $contact->date    = Carbon::parse($record["Date"]);
                $contact->type    = $method;
                $contact->subject = $record['Name'];
                $contact->private = false;
                $contact->followup = false;
                $contact->created_by = $case->user_id;
                $contact->created_at = Carbon::parse($record["Date"]);
                $contact->save();
            }

            
            $contact->type    = $method;
            //dd($method);

            $notes = "";

            $notes .= $case->notes."\n\n";

            if ($record["Steps Taken"]) {
                $notes .= "Steps Taken: ".$record["Steps Taken"]."\n";
            }
            if ($record["Outreach to Agency/Department\nContact Information"]) {
                $notes .= "Outreach to Agency/Department: ".$record["Outreach to Agency/Department\nContact Information"]."\n\n";
            }
            if ($record["Follow Up with Constituent"]) {
                $notes .= "Follow Up with Constituent: ".$record["Follow Up with Constituent"]."\n\n";
            }
            if ($record["Staffer Initials \nDate of Latest Update"]) {
                $notes .= $record["Staffer Initials \nDate of Latest Update"]."\n\n";
            }
            $contact->notes = $notes;
            //dd($contact, $record);
            $contact->save();

            // ATTACH CONSTITUENTS

            $first = "";
            $last = "";

            $name = $record['Name'];
            $name_arr = explode(' ', $name);

            if (count($name_arr) == 1) {
                $first = $name;
            }
            if (count($name_arr) == 2) {
                $first = $name_arr[0];
                $last  = $name_arr[1];
            }
            if (count($name_arr) == 3) {
                $first = $name_arr[0];
                $last  = $name_arr[2];
            }

            if (count($name_arr) > 3) {
                $first = $name_arr[0];
                $last  = $name_arr[1];
                if (strpos('a'.$contact->notes, $name) > 0) {

                } else {
                    $contact->notes = $name."\n".$contact->notes;
                    $contact->save();
                }
            } 

            $person = null;


            if ($first || $last) {
                $match = Person::where('team_id', 180)
                                ->where('first_name', $first)
                                ->where('last_name', $last)
                                ->get();

                if ($match) {
                    if ($match->count() == 1) {
                        $person = $match->first();
                        echo "Found Person: ".$name."\n";
                    }
                }

                if (!$person) {
                    $match = Voter::where('first_name', $first)
                                  ->where('last_name', $last)
                                  ->get();
                    
                    if ($match->count() == 1) {
                        // only one person it could be!
                        $person = findPersonOrImportVoter($match->first()->id, 180, true);
                        $person->created_at = $contact->date;
                        $person->save();
                        echo "Person from voter: ".$name."\n";
                    }
                }
            }
            if (!$person) {
                $person = new Person;
                $person->team_id = $case->team_id;
                $person->full_name = $name;
                $person->first_name = $first;
                $person->last_name = $last;
                $person->full_address = $record['Address'];
                $person->created_at = $contact->date;
                $person->save();
                echo "Created new person: ".$name."\n";
            }

            if ($person) {

                $contact_person = ContactPerson::where('contact_id', $contact->id)
                                               ->where('person_id', $person->id)
                                               ->first();
                if (!$contact_person) {
                    $contact_person = new ContactPerson;
                    $contact_person->team_id = $contact->team_id;
                    $contact_person->voter_id = $person->voter_id;
                    $contact_person->contact_id = $contact->id;
                    $contact_person->person_id = $person->id;
                    $contact_person->save();
                }

                $case_person = CasePerson::where('case_id', $case->id)
                                         ->where('person_id', $person->id)
                                         ->first();
                if (!$case_person) {
                    $case_person = new CasePerson;
                    $case_person->team_id = $case->team_id;
                    $case_person->voter_id = $person->voter_id;
                    $case_person->case_id = $case->id;
                    $case_person->person_id = $person->id;
                    $case_person->save();
                }
            }

            //dd($case, $contact, $person);
        }


        //dd($full_data);

        // =======================================> RESOLVED CASES


        $active = storage_path()."/app/csvs/smitty-resolved.csv";
        $full_data = $this->getFullData($active);

        //dd($full_data);

        foreach ($full_data as $record) {
            $case = WorkCase::where('team_id', 180)
                            ->where('date', Carbon::parse($record["Date"]))
                            ->where('subject', $record['Name'])
                            ->first();

            if (!$case) {
                $case = new WorkCase;
                $case->team_id  = 180;
                $case->date     = Carbon::parse($record["Date"]);
                $case->subject  = $record['Name'];
                $case->private  = false;
                $case->priority = null;
                $case->type     = $record['Issue'];
                $case->subtype  = null;
                $case->status   = 'resolved';
                $case->closing_remarks = $record['Resolution'];

                if (strpos('a'.$staff, 'RBM') > 0) {
                    $case->user_id = 1025;
                } else if (strpos('a'.$staff, 'JLM') > 0) {
                    $case->user_id = 1024;
                }
                if (!$case->user_id) {
                    $case->user_id = 384;
                }

                $case->save();
                //dd($case, $record);
            }


            $case->created_at = Carbon::parse($record["Date"]);

            if (!$case->notes) {
                $notes = "";
                $notes .= trim($record['Notes'])."\n";
                $notes .= trim($record['Address'])."\n";
                $notes .= trim($record['Contact Infomation'])."\n";
                $notes .= trim($record['Other Information'])."\n";

                $case->notes = trim($notes);
            }

            $staff = $record["Staffer Initials Date of Last Update"];

            if (strpos('a'.$staff, 'RBM') > 0) {
                $case->user_id = 1025;
            } else if (strpos('a'.$staff, 'JLM') > 0) {
                $case->user_id = 1024;
            }
            if (!$case->user_id) {
                $case->user_id = 384;
            }
            
            $case->save();
            //dd($case);

            $method = "";
            if ($record["Method of Inquiry"]) {
                if (strpos('a'.$record["Method of Inquiry"], 'Phone') > 0) {
                    $method = 'Phone';
                } else if (strpos('a'.$record["Method of Inquiry"], 'Email') > 0) {
                    $method = 'Email';
                }
            }

            $contact = Contact::where('team_id', 180)
                            ->where('user_id', $case->user_id)
                            ->where('date', Carbon::parse($record["Date"]))
                            ->where('subject', $record['Name'])
                            ->first();
            if (!$contact) {
                
                $contact = new Contact;
                $contact->team_id = 180;
                $contact->user_id = $case->user_id;
                $contact->case_id = $case->id;
                $contact->date    = Carbon::parse($record["Date"]);
                $contact->type    = $method;
                $contact->subject = $record['Name'];
                $contact->private = false;
                $contact->followup = false;
                $contact->created_by = $case->user_id;
                $contact->created_at = Carbon::parse($record["Date"]);
                $contact->save();
            }

            
            $contact->type    = $method;
            //dd($method);

            $notes = "";

            $notes .= $case->notes."\n\n";

            if ($record["Steps Taken"]) {
                $notes .= "Steps Taken: ".$record["Steps Taken"]."\n";
            }
            if ($record["Outreach to Agency/ Department Contact information"]) {
                $notes .= "Outreach to Agency/Department: ".$record["Outreach to Agency/ Department Contact information"]."\n\n";
            }
            if ($record["Follow Up with Consituent"]) {
                $notes .= "Follow Up with Constituent: ".$record["Follow Up with Consituent"]."\n\n";
            }
            if ($record["Staffer Initials Date of Last Update"]) {
                $notes .= $record["Staffer Initials Date of Last Update"]."\n\n";
            }
            $contact->notes = $notes;
            //dd($contact, $record);
            $contact->save();

            // ATTACH CONSTITUENTS

            $first = "";
            $last = "";

            $name = $record['Name'];
            $name_arr = explode(' ', $name);

            if (count($name_arr) == 1) {
                $first = $name;
            }
            if (count($name_arr) == 2) {
                $first = $name_arr[0];
                $last  = $name_arr[1];
            }
            if (count($name_arr) == 3) {
                $first = $name_arr[0];
                $last  = $name_arr[2];
            }

            if (count($name_arr) > 3) {
                $first = $name_arr[0];
                $last  = $name_arr[1];
                if (strpos('a'.$contact->notes, $name) > 0) {

                } else {
                    $contact->notes = $name."\n".$contact->notes;
                    $contact->save();
                }
            } 

            $person = null;


            if ($first || $last) {
                $match = Person::where('team_id', 180)
                                ->where('first_name', $first)
                                ->where('last_name', $last)
                                ->get();

                if ($match) {
                    if ($match->count() == 1) {
                        $person = $match->first();
                        echo "Found Person: ".$name."\n";
                    }
                }

                if (!$person) {
                    $match = Voter::where('first_name', $first)
                                  ->where('last_name', $last)
                                  ->get();
                    
                    if ($match->count() == 1) {
                        // only one person it could be!
                        $person = findPersonOrImportVoter($match->first()->id, 180, true);
                        $person->created_at = $contact->date;
                        $person->save();
                        echo "Person from voter: ".$name."\n";
                    }
                }
            }
            if (!$person) {
                $person = new Person;
                $person->team_id = $case->team_id;
                $person->full_name = $name;
                $person->first_name = $first;
                $person->last_name = $last;
                $person->full_address = $record['Address'];
                $person->created_at = $contact->date;
                $person->save();
                echo "Created new person: ".$name."\n";
            }

            if ($person) {

                $contact_person = ContactPerson::where('contact_id', $contact->id)
                                               ->where('person_id', $person->id)
                                               ->first();
                if (!$contact_person) {
                    $contact_person = new ContactPerson;
                    $contact_person->team_id = $contact->team_id;
                    $contact_person->voter_id = $person->voter_id;
                    $contact_person->contact_id = $contact->id;
                    $contact_person->person_id = $person->id;
                    $contact_person->save();
                }

                $case_person = CasePerson::where('case_id', $case->id)
                                         ->where('person_id', $person->id)
                                         ->first();
                if (!$case_person) {
                    $case_person = new CasePerson;
                    $case_person->team_id = $case->team_id;
                    $case_person->voter_id = $person->voter_id;
                    $case_person->case_id = $case->id;
                    $case_person->person_id = $person->id;
                    $case_person->save();
                }
            }

            //dd($case, $contact, $person);
        }

        return Command::SUCCESS;
    }

    public function getFullData($file)
    {
        $row = 1;
        $full_data = [];
        $fields = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                if ($row == 1) {
                    foreach ($data as $tempkey => $val) {
                        $data[$tempkey] = trim($val);
                    }
                    $fields = $data;
                    $row++;
                    continue;
                }
                
                $onerow = [];
                foreach ($data as $key => $val) {
                    $onerow[$fields[$key]] = trim($val);
                }
                $full_data[] = $onerow;
                $row++;
                
            }
            //dd($full_data);
            fclose($handle);
        }
        return $full_data;
    }

    /* 
        ========================> CASES
        * team_id
        * user_id
        * priority
        * private
        * date
        * type
        * subtype
        * status
        * subject
        * notes
        * closing_remarks

        ========================> CONTACTS
        * team_id
        * user_id
        * case_id
        * date
        * type
        * subject
        * notes
        * private
        * followup
        * created_by

        ========================> SMITTY DATA
        148 => array:13 [
            * "Date" => "5/11"
            * "Name " => "Marsha Venne"
            * "Address" => "191 Main St Russel"
            * "Contact Information" => "413-769-5853"
            * "Method of Inquiry" => "Phone"
            * "Other Information " => "Claimaint ID:  10202219"
            * "Issue" => "Unemployment"
            * "Notes " => "Approved but waiting on confirmation from last employer. "
            "Steps Taken" => "Trey contacted unemployment"
            "Outreach to Agency/Department\nContact Information" => "Deemed elligible"
            "Follow Up with Constituent " => "Called Marsha to let her know payment process should begin in 2-3 days"
            * "Resolution " => "Resolved"
            "Staffer Initials \nDate of Latest Update" => "RBM 5/12"
          ]
    */
}
