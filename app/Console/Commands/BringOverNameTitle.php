<?php

namespace App\Console\Commands;

use App\Person;
use Illuminate\Console\Command;

class BringOverNameTitle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:name_title';

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
        $count = 0;
        Person::whereNull('name_title')->with('voterMaster')->chunk(1000, function ($people) use (&$count) {
            echo $count."\n";
            foreach ($people as $person) {
                if ($person->voterMaster) {
                    echo $person->voter_id."\n";
                    if ($person->voterMaster->name_title) {
                        $person->name_title = $person->voterMaster->name_title;
                        $person->save();
                        echo $person->name_title.' '.$person->name."\n";
                    }
                }
            }
        });
    }
}
