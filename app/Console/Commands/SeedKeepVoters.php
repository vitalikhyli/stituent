<?php

namespace App\Console\Commands;

use Artisan;
use Carbon\Carbon;
use Database\Seeders\Faker\FakerSeeder;
use Database\Seeders\Faker\PresetSeeder;
use Database\Seeders\Faker\VoterFileSeeder;
use Database\Seeders\NortheasternUniversitySeeder;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class SeedKeepVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeds without re-importing voterfile';

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
    public function seedMessage($opening_message)
    {
        echo str_repeat('-', 76)."\r\n";
        $spacer = (76 - strlen($opening_message)) / 2;
        echo str_repeat(' ', $spacer).$opening_message."\r\n";
        echo str_repeat('-', 76)."\r\n";
    }

    public function handle()
    {

        // if ($this->confirm('Do you want to a full migration?')) {

        //     Artisan::call('migrate:fresh', array('--seed' => true));

        //     dd('done');

        // }

        $insult = "Hey!!!!! \r\n\r\n";
        $neu_mode = false;
        $migrate = false;
        $voter_files = false;

        if ($this->confirm($insult.'Do you want to migrate:fresh?')) {
            $migrate = true;
        }

        if ($this->confirm($insult.'Do you want to seed the VOTER FILES?')) {
            $voter_files = true;
        }

        if ($this->confirm($insult.'Do you want to ONLY seed Northeastern University?')) {
            global $neu_mode;
            $neu_mode = true;
        }

        $start = Carbon::now();

        $tables = DB::select('SHOW TABLES');

        if ($migrate == true) {
            $this->seedMessage('Starting Migration...');
            Artisan::call('migrate:fresh');
        } else {
            $this->seedMessage('Starting CF:SEED');

            echo "Truncating tables (except for Voter tables) \r\n";

            $t = 1;

            foreach ($tables as $table) {
                foreach ($table as $key => $table_name) {
                    if (substr($table_name, 0, 2) != 'x_') {
                        if (substr($table_name, 0, 2) != 'z_') {
                            DB::table($table_name)->truncate();
                            echo '  truncating '.$t.': '.$table_name." \r\n";

                            $t++;
                        }
                    }
                }
            }

            echo "  ****************************************|  Done. \r\n";
        }

        echo "Running Preset Seeder \r\n";
        Artisan::call('db:seed', ['--class' => 'PresetSeeder']);

        echo "Running NEU Seeder \r\n";
        Artisan::call('db:seed', ['--class' => 'NortheasternUniversitySeeder']);

        if ($voter_files) {
            $this->seedMessage('Running VoterFile Seeder');

            $tables = DB::select('SHOW TABLES');

            $t = 1;

            foreach ($tables as $table) {
                foreach ($table as $key => $table_name) {
                    if ((substr($table_name, 0, 2) == 'x_') ||
                        (substr($table_name, 0, 2) == 'z_')) {

                        //echo "  dropping ".$t.": ".$table_name." \r\n";
                        //Schema::dropIfExists($table_name);

                        DB::table($table_name)->truncate();
                        echo '  truncating '.$t.': '.$table_name." \r\n";

                        $t++;
                    }
                }
            }

            Artisan::call('db:seed', ['--class' => 'VoterFileSeeder']);

            echo "\r\n";
            $this->seedMessage('Starting Worker...');

            Artisan::call('st:work');
        }

        echo "Running Faker Seeder \r\n";
        Artisan::call('db:seed', ['--class' => 'FakerSeeder']);

        $duration = Carbon::now()->diffInSeconds($start);

        $this->seedMessage('CF:SEED took a grand total of '.$duration.' seconds.');
    }
}
