<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Voter;
use App\VoterSlice;

use Schema;


class Birthdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:birthdays';

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

        foreach(VoterSlice::all() as $slice) {

            if (!Schema::hasTable($slice->name)) {
                $this->info('**** Table '.$slice->name.' does not exist');
                continue;
            }

            $this->info($slice->name);

            session()->put('team_table', $slice->name);

            $slice->birthdays = $this->getBirthdays();
            $slice->save();

        }

    }

    public function getBirthdays()
    {
        $ids = [];
        for ($i=0; $i < 135000; $i++) { 
            $ids[] = Voter::inRandomOrder()->first()->id;
            echo "    ".$i."\r";
        }

        return $ids;
    }

}
