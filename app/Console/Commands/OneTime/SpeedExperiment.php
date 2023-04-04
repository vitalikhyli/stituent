<?php

namespace App\Console\Commands\OneTime;

use App\Contact;
use App\People;
use App\User;
use App\Voter;
use Illuminate\Console\Command;

class SpeedExperiment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:speed_experiment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests alternatives to if:count>0';

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
        session()->put('team_table', User::where('username', 'fluency1')->first()->team->db_slice);

        foreach ([500, 1000, 5000, 10000, 20000] as $amount) {
            $this->info("Running experiment {$amount} times.");

            $this->runExperiments($amount);
        }
    }

    public function runExperiments($amount)
    {

        // $user = User::where('username', 'fluency1')->first();

        //////////////////////////////////////////////////////

        $start = microtime(true);

        for ($i = 0; $i < $amount; $i++) {

            // if ($user->contacts()->count() > 0) { true; }
            if (Voter::count() > 0) {
                true;
            }
        }

        $this->showElapsed('count() > 0', $start, $amount);

        //////////////////////////////////////////////////////

        $start = microtime(true);

        for ($i = 0; $i < $amount; $i++) {

            // if ($user->contacts()->first()) { true; }
            if (Voter::first()) {
                true;
            }
        }

        $this->showElapsed('first()', $start, $amount);

        //////////////////////////////////////////////////////

        $start = microtime(true);

        for ($i = 0; $i < $amount; $i++) {

            // if ($user->contacts()->limit(1)->count() > 0) { true; }
            if (Voter::limit(1)->count() > 0) {
                true;
            }
        }

        $this->showElapsed('limit(1)->count() > 0', $start, $amount);
    }

    public function showElapsed($title, $start, $amount)
    {
        $title = '@ '.str_pad($amount, 6, ' ', STR_PAD_RIGHT).' : '.str_pad($title, 25, ' ', STR_PAD_RIGHT);

        echo str_repeat('-', 60)."\r\n";
        $time_elapsed_secs = microtime(true) - $start;
        $time_elapsed_secs_each = $time_elapsed_secs / $amount;
        echo $title.' - Elapsed: '.$time_elapsed_secs_each.' secs'."\r\n";
        // echo str_repeat('-', 60)."\r\n";
    }
}
