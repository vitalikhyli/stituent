<?php

namespace Database\Seeders\Faker;

use App\Jobs\StartWorker;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Faker\FakerSeeder;
use Database\Seeders\Faker\OriginalSeeder;
use Database\Seeders\Faker\PresetSeeder;
use Database\Seeders\Faker\VoterFileSeeder;
use Database\Seeders\NortheasternUniversitySeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class OriginalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public $neu_mode = false;

    public function TeamSwitch($mode)
    {
        // In order to populate some faker models with both teams
        // So we can switch back and forth in development
        $team_a = User::where('id', 2)->first();
        if ($team_a->current_team_id == 1) {
            $team_a->current_team_id = 2;
        } else {
            $team_a->current_team_id = 1;
        }
        $team_a->save();

        $team_b = User::where('id', 1)->first();
        if ($team_b->current_team_id == 1) {
            $team_b->current_team_id = 2;
        } else {
            $team_b->current_team_id = 1;
        }
        $team_b->save();

        if ($mode == '') {
            $mode = 'Switching teams';
        }
        echo $mode."...\r\n";
    }

    public function run()
    {

        // $start = \Carbon\Carbon::now();
        $s = new DatabaseSeeder;

        $start = $s->seederStart();

        // ////////////////////////////////////////////////////////////////////////////
        // //
        // //  FOR SPEED?
        // //

        // $insult = "Hey!!!!! \r\n\r\n";

        // if ($this->command->confirm($insult.'Do you want to ONLY seed Northeastern University?')) {

        //     global $neu_mode;
        //     $neu_mode = true;

        // }

        ////////////////////////////////////////////////////////////////////////////
        //
        //  DATA TO SET UP BASICS
        //

        $never_do_it_again = 0;

        if (($never_do_it_again == 0) || (config('app.env') != 'production')) {
            echo str_repeat('-', 76)."\r\n";
            $this->call([
                PresetSeeder::class,
            ]);
            $this->call([
                NortheasternUniversitySeeder::class,
            ]);
        }

        ////////////////////////////////////////////////////////////////////////////
        //
        //  LOCAL FAKER DATA
        //

        if (config('app.env') != 'production') {

            // Always skip now because starting to use real voter data
            $this->skip_voter_files = true;

            if (! $this->skip_voter_files) {
                echo str_repeat('-', 76)."\r\n";
                $this->call([
                    VoterFileSeeder::class,
                ]);

                echo str_repeat('-', 76)."\r\n";
                $this->command->info('Running Worker...');
                Artisan::call('st:work');

                $middle = \Carbon\Carbon::now();
                $duration = $middle->diffInSeconds($start);
                $duration = round($duration / 60, 2);
                echo str_repeat('-', 76)."\r\n";
                $grand_total = '* So far, the process has taken '.$duration.' minutes *';
                $spacer = (76 - strlen($grand_total)) / 2;
                echo str_repeat(' ', $spacer).$grand_total."\r\n";
                echo str_repeat('-', 76)."\r\n";
            }

            //This will be put in by the users in production

            $this->call([
                FakerSeeder::class,
            ]);

            // (new FakerSeeder)->run($this->neu_mode);
        }

        ////////////////////////////////////////////////////////////////////////////
        //
        //  END
        //

        $s->seederFinish($start);

        // $end = \Carbon\Carbon::now();
        // $duration = $end->diffInSeconds($start);
        // $duration = round($duration/60,2);
        // echo str_repeat('-',76)."\r\n";
        // $grand_total = "Seeding took a grand total of ".$duration." minutes.";
        // $spacer = (76 - strlen($grand_total))/2;
        // echo str_repeat(' ',$spacer).$grand_total."\r\n";
        // echo str_repeat('-',76)."\r\n";
    }
}
