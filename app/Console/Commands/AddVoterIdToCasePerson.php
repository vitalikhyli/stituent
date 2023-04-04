<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CasePerson;

class AddVoterIdToCasePerson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:add_voter_id_to_case_person';

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
        $case_people = CasePerson::with('person')->orderBy('person_id')->get();
        echo "about to process ".$case_people->count()." case_person records \n";
        $added = 0;
        foreach ($case_people as $key => $cp) {
            $person = $cp->person;
            if ($person->voter_id) {
                $cp->voter_id = $person->voter_id;
                $cp->save();
                $added++;
            }
            echo "Completed: $key, Added: $added\r";
        }
    }
}
