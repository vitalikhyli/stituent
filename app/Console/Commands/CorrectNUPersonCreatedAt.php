<?php

namespace App\Console\Commands;

use App\Person;
use Illuminate\Console\Command;

class CorrectNUPersonCreatedAt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:correct_nu_person';

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
     * @return mixed
     */
    public function handle()
    {
        foreach (Person::where('team_id', 142)->get() as $person) {
            $case = $person->cases()->first();
            $person->created_at = $case->date;
            $person->save();
        }
    }
}
