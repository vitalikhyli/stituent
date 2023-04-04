<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;

use App\District;


class NullStatesToMA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:null_states_districts';

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

        $this->info('There are '.District::count().' rows in the District table.');

        if(District::whereNull('state')->exists()) {

            $go = $this->confirm('There are '.District::whereNull('state')->count().' rows with null states in the District table. Change all nulls to MA?');

            if($go) {
                foreach(District::whereNull('state')->get() as $district) {
                    $this->info($district->name);
                    $district->state = 'MA';
                    $district->save();
                }
            }

        }

        $this->info('There are '.District::count().' rows in the District table.');

        $this->info('There are '.District::whereNull('state')->count().' rows with null states in the District table.');
    }
}
