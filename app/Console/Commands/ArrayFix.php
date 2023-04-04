<?php

namespace App\Console\Commands;

use App\Person;
use App\Team;
use Illuminate\Console\Command;

class ArrayFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:array_fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Problems with other_emails and other_phones';

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
    public function checkArrayStructure($a)
    {
        if (is_array($a)) {                         // FIRST LEVEL IS AN ARRAY

            if (count($a) == 1) {                   // ...THAT HAS ONLY ONE ELEMENT

                if (isset($a[0])) {
                    if (is_array($a[0])) {          // ...THE SOLE ELEMENT IS ITSELF AN ARRAY

                        if (count($a[0]) == 1) {    // ...THAT HAS ONLY ONE ELEMENTS

                            echo "**** Double nested array found.\r\n";

                            return $a[0];           // RETURN THE SOLE NESTED ARRAY
                        }
                    }
                }
            }
        }
    }

    public function handle()
    {
        echo "\r\n";
        echo '-------------------------------------'."\r\n";
        echo 'Reviewing arrays'."\r\n";

        $problems = 0;
        $solutions = [];

        // $teams = Team::where('name', 'All Campaigns')->get();
        $teams = Team::all();

        foreach ($teams as $team) {
            $people = Person::where('team_id', $team->id)->get();
            foreach ($people as $person) {
                $correction = $this->checkArrayStructure($person->other_emails);
                if ($correction) {
                    $problems++;
                    if (is_array($correction)) {
                        //dd($correction);
                    }
                    echo 'PROBLEM WITH EMAIL ARRAY '.$person->full_name."\r\n";
                    //print_r($person->other_emails);
                    $solutions[] = ['person_id' => $person->id, 'other_emails' => $correction];
                }

                $correction = $this->checkArrayStructure($person->other_phones);
                if ($correction) {
                    $problems++;
                    if (is_array($correction)) {
                        //dd($correction);
                    }
                    echo 'PROBLEM WITH PHONE ARRAY '.$person->full_name."\r\n";
                    //print_r($person->other_phones);
                    $solutions[] = ['person_id' => $person->id, 'other_phones' => $correction];
                }
            }
        }

        echo '-------------------------------------'."\r\n";

        echo '# Problems = '.$problems."\r\n";

        foreach ($solutions as $solution) {
            $person = Person::find($solution['person_id']);
            echo 'UPDATING Person '.$person->id."\r\n";
            if (isset($solution['other_emails'])) {
                $person->other_emails = $solution['other_emails'];
            }
            if (isset($solution['other_phones'])) {
                $person->other_phones = $solution['other_phones'];
            }
            $person->save(); //
        }
    }
}
