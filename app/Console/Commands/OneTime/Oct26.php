<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

use App\Person;
use App\Contact;
use App\ContactPerson;
use App\Team;
use App\Voter;
use App\User;

use Carbon\Carbon;


class Oct26 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:oct26';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public $delimiter;
    public $limit;
    public $team_id;
    public $user_id;
    public $test_mode;

    public function __construct()
    {
        parent::__construct();

        set_time_limit(-1);

        $this->delimiter    = ',';
        $this->limit        = 10000;

        if (config('app.env') == 'local') {

            $this->team_id = 1; // Testing
            //$this->user_id = Team::find($this->team_id)->users()->orderBy('id', 'desc')->first()->id;

        } else {

            $this->team_id = 196; // Soter
            $this->user_id = 784; // Eric Eisner

        }

        

        //session()->put('team_table', Team::find($this->team_id)->db_slice);

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mode = $this->choice('Mode?', ['Test (Recommended)', 'Save'], 1);
        $this->test_mode = ($mode == 'Save') ? false : true;

        if ($this->test_mode) {
            echo str_repeat('*', 70)."\r\n";
            echo 'TEST MODE, CHIEF'."\r\n";
            echo str_repeat('*', 70)."\r\n";
        }

        if (!$this->test_mode) {
            $this->info(str_repeat('*', 70));
            $this->info('SAVE MODE, CHIEF');
            $this->info(str_repeat('*', 70));
        }

        $run = $this->confirm('Run for TEAM: '.Team::find($this->team_id)->name.' USER: '.User::find($this->user_id)->name.'?');
        if (!$run) dd('Exited.');

        $file_name = 'constituent-list-formatted.csv';
        $team_folder = 'team_'.str_pad($this->team_id, 5, 0, STR_PAD_LEFT);
        $path = storage_path().'/app/user_files/office/'.$team_folder.'/'.$file_name;

        if (!file_exists($path)) {
            dd("File doesn't exist, dummy: ".$path);
        }

        $run = $this->confirm('Import '.$path.'?');
        if (!$run) dd('Exited.');

        $file = new \SplFileObject($path, 'r');
        $firstrow = $file->fgetcsv($this->delimiter);

        $unique_list = [];
        foreach($firstrow as $key => $column) {
            if (in_array($column, $unique_list)) {
                $firstrow[$key] = strtolower('contact_'.$column); // Second time this column appears
            } else {
                $firstrow[$key] = strtolower($column);
            }
            $unique_list[] = $column;
        }

        $rows = [];
        for ($i = 0; $i < $this->limit; $i++) {
            $rawrow = $file->fgetcsv($this->delimiter);
            if ($rawrow) {
                $row = [];
                foreach ($rawrow as $key => $val) {
                    if (implode('', $rawrow) != '') { // If everything not blank
                        $row[$firstrow[$key]] = trim($val);
                    }
                    
                }
                if  (!empty($row)) {
                    $rows[] = $row;
                }
            }
        }

        foreach ($rows as $row_number => $row) {

            try {

                $this->importRow($row, $row_number);

            } catch (\Exception $e) {

                dd($row['last name'].', '.$row['first'].': '.$e->getMessage());

            }

        }

    }

    public function importRow($data, $row_number) {

        //dd($data);

        // array:16 [
        //   0 => "date"
        //   1 => "last name"
        //   2 => "first"
        //   3 => "address"
        //   4 => "town"
        //   5 => "phone"
        //   6 => "email"
        //   7 => "cell"
        //   8 => "code"
        //   9 => "dept."
        //   10 => "contact"
        //   11 => "contact_phone"
        //   12 => "contact_email"
        //   13 => "notes"
        //   14 => "status"
        //   15 => "follow-up letter"
        // ]

        $date_str = $data['date'];
        try {
            $date = Carbon::parse($date_str);
        } catch (\Exception $e) {
            $date_arr = explode(' ', $date_str);
            $first_date_str = $date_arr[0];

            try {
                $date = Carbon::parse($first_date_str);
            } catch (\Exception $e) {
                
                dd($e->getMessage(), $first_date_str);
            }
            $data['date'] = $first_date_str;

        }
        $town_str = trim($data['town']);
        $town_str = str_replace(' MA', '', $town_str);
        $town_str = str_replace(',', '', $town_str);
        $town_str = str_replace(' RI', '', $town_str);
        $town_str = str_replace(' CT', '', $town_str);
        $town_str = str_replace(['02895', '02864', '02019', '01538', '02376'], '', $town_str);
        $data['town'] = $town_str;

        $import_tag = 'Custom import from file, row #';

        $person = Person::where('team_id', $this->team_id)
                        ->where('last_name', $data['last name'])
                        ->where('first_name', $data['first'])
                        ->first();

        if (!$person) {

            $person = new Person;
            $person->team_id        = $this->team_id;
            $person->last_name      = $data['last name'];
            $person->first_name     = $data['first'];
            
            

            $address = $this->parseAddress($data['address']);
            $person->address_number = $address['number'];
            $person->address_street = $address['street'];
            $person->address_apt    = $address['apt'];
            $person->address_city = $data['town'];
            $person->address_state  = 'MA';

            $voter = $this->findVoter($person);
            if ($voter) {
                $this->info('Found Voter ID: '.$voter->id);
                $person = findPersonOrImportVoter($voter->id, $this->team_id, $dontsave = true);
            } else {
                $this->info('No Voter found: '.$person->first_name." ".$person->last_name);
            }

            $person->team_id        = $this->team_id;
            $person->last_name      = $data['last name'];
            $person->first_name     = $data['first'];

            $address = $this->parseAddress($data['address']);
            $person->address_number = $address['number'];
            $person->address_street = $address['street'];
            $person->address_apt    = $address['apt'];
            $person->address_city = $data['town'];
            $person->address_state  = 'MA';
            $person->primary_phone  = $data['phone'];
            $person->primary_email  = $data['email'];
            if ($data['cell']) $person->other_phones   = ['mobile' => $data['cell']];
            $person->created_at     = Carbon::parse($data['date'])->toDateString();

            if (!$this->test_mode) $person->save();

            $this->info($person->full_name);

        } else {

            if (!$person->primary_phone) {
                $person->primary_phone  = $data['phone'];
            }
            if (!$person->primary_email) {
                $person->primary_email  = $data['email'];
            }
            echo $person->full_name.' already exists: '.$person->id."\r\n";

        }

        //dd("done");


        $contact = Contact::where('team_id', $this->team_id)
                          ->where('user_id', $this->user_id)
                          ->whereDate('created_at', Carbon::parse($data['date']))
                          ->where('notes', 'like', '%'.$import_tag.$row_number.'%')
                          ->first();

        if (!$contact) {

            $contact = new Contact;
            $contact->team_id       = $this->team_id;
            $contact->user_id       = $this->user_id;
            $contact->date          = Carbon::parse($data['date'])->toDateString();
            $contact->notes         = $data['notes'];
            
            if ($data['dept.']) {
                $contact->notes .= "\n\n".'Dept: '.$data['dept.'];
            }
            if ($data['contact']) {
                $contact->notes .= "\n\n".'Contact: '.$data['contact'];
            }
            if ($data['contact_phone']) {
                $contact->notes .= "\n\n".'Contact Phone: '.$data['contact_phone'];
            }
            if ($data['contact_email']) {
                $contact->notes .= "\n\n".'Contact Email: '.$data['contact_email'];
            }
            if ($data['status']) {
                $contact->notes .= "\n\n".'Status: '.$data['status'];
            }
            if ($data['follow-up letter']) {
                $contact->notes .= "\n\n".'Follow-up letter: '.$data['follow-up letter'];
            }

            $contact->notes         .= "\n\n".$import_tag.$row_number;
            $contact->created_at    = Carbon::parse($data['date'])->toDateString();
            
            if (!$this->test_mode) $contact->save();

            $this->info('Contact '.Carbon::parse($contact->date)->format('m/d/y'));

        } else {

            echo 'Contact already exists: '.$contact->id."\r\n";
            
        }
        //dd("done");

        if ($contact && $person) {

            $pivot = ContactPerson::where('team_id', $this->team_id)
                                  ->where('person_id', $person->id)
                                  ->where('contact_id', $contact->id)
                                  ->first();

            if (!$pivot) {

                $pivot = new ContactPerson;
                $pivot->team_id     = $this->team_id;
                $pivot->person_id   = $person->id;
                $pivot->contact_id  = $contact->id;
                $pivot->voter_id    = $person->voter_id;
                
                if (!$this->test_mode) $pivot->save();

                $this->info('ContactPerson created.');
                echo "\n";

            } else {

                echo 'ContactPerson already exists: '.$pivot->id."\r\n\r\n";
                
            }

        }

        if ($this->test_mode) {

            print_r($person->getAttributes());
            print_r($contact->getAttributes());

            $this->info(str_repeat('-', 70));
        }


    }

    public function parseAddress($address)
    {
        // 175 R Farm Street
        // 7 Farnum Street
        // Depot Court Apt 6-61
        // Depot Court Apt. 6-71
        // Need to get this
        // Calument Ct Unit 15
        // Calument Ct Unit 8
        // Need to get this info you have it Eric
        // 30 Smith Street
        // 51 Essex St. 
        // 246 Elm St.
        // 146 North St.
        // 4 Adams Ct
        // 116 Providence St.
        // 652 Blackstone St.
        // 19 Concord Ln 

        $words = collect(explode(' ', $address))->reverse();
        $str = null;

        $number         = null;
        $apt            = null;
        $remainder      = null;

        foreach($words as $key => $word) {

            $str = $word.' '.$str; // Re-build address going backwards from end

            if (in_array($word, ['Apt', 'Apt.', 'Unit', '#'])) {
                $apt = trim($str);
            }

            if ($key == 0 && is_numeric($word)) {
                $number = trim($word);
            }

        }

        $remainder = trim(str_replace($number, '', str_replace($apt, '', $address)));

        return [
                'apt'       => $apt,
                'street'    => $remainder,
                'number'    => $number
                ];
    }

    public function findVoter($person)
    {
        if ($person->voter_id) {
            return Voter::find($person->voter_id);
        }
        $voter_collection = Voter::where('last_name', $person->last_name)
                      ->where('first_name', $person->first_name)
                      ->get();

        if ($voter_collection->count() > 1) {
            $voter_collection_city = Voter::where('last_name', $person->last_name)
                      ->where('first_name', $person->first_name)
                      ->where('address_city', $person->address_city)
                      ->get();
            if ($voter_collection_city->count() > 1) {

                echo "Multiple ".$person->name." in ".$person->address_city."\n";
            } else if ($voter_collection_city->count() == 1) {
                return $voter_collection_city->first();
            }
            return null;
        }
        if ($voter_collection->count() == 1) {
            return $voter_collection->first();
        }
        return null;
    }
}
