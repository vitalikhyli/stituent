<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

use App\Municipality;
use App\VoterMaster;


class Devens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:devens';

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

        //////////////////// One-Time Clean-Up

        $devens = Municipality::where('name', 'Devens')->where('state', 'MA')->first();

        if (!$devens) {

            $this->info('No Municipalities called "Devens"');

        } else {

            $devens_count = \App\VoterMaster::where('city_code', $devens->code)->count();

            if ($devens_count == 0) {

                $go = $this->confirm('There are '.Municipality::where('state', 'MA')->count().' towns in the municipalities table. Massachusetts should have 351. There are 0 records in VoterMaster for the town of "Devens" (city_code '.$devens->code.') Delete this town from the municipalities table?');

                if($go) {

                    $devens->delete();

                    $this->info('Devens deleted from Municipalities.');
                    
                }

            }
        }
    }
}
