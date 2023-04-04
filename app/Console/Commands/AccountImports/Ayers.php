<?php

namespace App\Console\Commands\AccountImports;

use Illuminate\Console\Command;
use Carbon\Carbon;

use App\GroupPerson;
use App\Group;
use App\Person;
use App\Voter;
use App\Category;

class Ayers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:import_ayers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports Ayers excel sheet to group';

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
        $active = storage_path()."/app/csvs/ayers-xmas.csv";
        $full_data = $this->getFullData($active);

        //dd($full_data);

        $group = Group::where('team_id', 264)
                      ->where('name', 'Xmas Card List 2022')
                      ->first();

        $category = Category::where('name', 'Constituent Groups')->first();
        if (!$category) {
            $category = new Category;
            $category->name = 'Constituent Groups';
            $category->team_id = 264;
            $category->save();
        }
        if (!$group) {
            $group = new Group;
            $group->category_id = $category->id;
            $group->name = 'Xmas Card List 2022';
            $group->team_id = 264;
            $group->save();
        }

        //dd($group);

        foreach ($full_data as $record) {
            
            //dd($record);
            // ATTACH CONSTITUENTS

            $first = trim($record['First Name']);
            $last = trim($record['Last Name']);
            $name = $first." ".$last;

            $person = null;


            if ($first || $last) {
                $match = Person::where('team_id', 264)
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
                        $person = findPersonOrImportVoter($match->first()->id, 264, true);
                        $person->save();
                        echo "Person from voter: ".$name."\n";
                    }
                }
            }
            $address_arr = explode(' ', $record['Address'], 2);
            //dd($address_arr);
            $num = $address_arr[0];
            $street = $address_arr[1];


            if (!$person) {
                $person = new Person;
                $person->team_id = 264;
                $person->full_name = $name;
                $person->first_name = $first;
                $person->middle_name = $record['Middle Name'];
                $person->last_name = $last;
                $person->suffix_name = $record['Suffix'];
                $person->address_number = $num;
                $person->address_street = $street;
                $person->address_zip = $record['mZip5'];
                $person->address_city = $record['City'];
                $person->address_state = $record['State'];
                $person->full_address = $record['Address'].', '.$record['City'].', '.$record['State']." ".$record['mZip5'];
                $person->save();
                echo "Created new person: ".$name."\n";
                //dd($person);
            }
            
            if ($person) {

                $group_person = GroupPerson::where('group_id', $group->id)
                                               ->where('person_id', $person->id)
                                               ->first();
                if (!$group_person) {
                    $group_person = new GroupPerson;
                    $group_person->team_id = $group->team_id;
                    $group_person->group_id = $group->id;
                    $group_person->person_id = $person->id;
                    $group_person->save();
                }
            }

            //dd($case, $group, $person);
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
