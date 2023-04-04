<?php

namespace App\Console\Commands;

use App\BadEmail;
use App\Person;
use Illuminate\Console\Command;

class ClearBadEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:clear_bad_emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes any primary_email from the people table in the bad_emails table';

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
        $bad_emails = [];
        $bad_emails_collection = BadEmail::all();
        foreach ($bad_emails_collection as $bad) {
            $bad_emails[$bad->email] = 1;
        }
        //dd($bad_emails);
        $count = 0;
        $corrected = 0;
        Person::whereNotNull('primary_email')->chunkById(1000, function ($people) use ($bad_emails, &$count, &$corrected) {
            //dd($people->count());
            echo $count."\r";
            foreach ($people as $person) {
                //echo $person->primary_email."\n";
                if (isset($bad_emails[$person->primary_email])) {
                    $corrected++;
                    echo $corrected.'. '.$person->team->name.': Corrected '.$person->primary_email.', Person ID '.$person->id."\n";
                    $bademail = BadEmail::where('email', $person->primary_email)->first();
                    $bademail->person_id = $person->id;
                    $bademail->save();
                    $person->primary_email = null;
                    $person->save();
                }
            }
            //dd();
            $count += 1000;
        });
    }
}
