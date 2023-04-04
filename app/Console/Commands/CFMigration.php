<?php

namespace App\Console\Commands;

use Artisan;
use Database\Seeders\Faker\OriginalSeeder;
use DB;
use Illuminate\Console\Command;

class CFMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:migration {--fresh} {--faker} {--neu} {--reverse} {--seed}';

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
        dd();
        if ($this->option('reverse')) {
            $this->info('Moving back in...');
            $this->moveVoterTables($reverse = true);
            exit;
        }

        $this->info('Moving out...');
        $this->moveVoterTables();

        $this->info('Migrating...');
        if ($this->option('fresh')) {
            $this->info('Migrating Fresh...');
            Artisan::call('migrate:fresh');
        } else {
            Artisan::call('migrate');
        }

        $this->info('Moving back in...');
        $this->moveVoterTables($reverse = true);

        if ($this->option('neu')) {
            global $neu_mode;
            $neu_mode = true;
            $this->info('Running the Original Seeder with Faker Data (NEU ONLY)');
            Artisan::call('db:seed', ['--class' => 'OriginalSeeder']);
        } elseif ($this->option('faker')) {
            $this->info('Running the Original Seeder with Faker Data');
            Artisan::call('db:seed', ['--class' => 'OriginalSeeder']);
        }

        if ($this->option('seed')) {
            Artisan::call('db:seed');
        }
    }

    public function moveVoterTables($reverse = null)
    {
        $included = []; //['voter_slices'];
        $included_prefixes = ['x_',
                               'z_',
                               'chat_', ];
        $excluded = ['x__template_households',
                               'x__template_voters',
                               'x_voters_MA_master',
                               'x_households_MA_master', ];
//
        if (! $reverse) {
            $from_db = env('DB_DATABASE');
            $to_db = env('DB_VOTER_DATABASE');
            $tables = DB::select('SHOW TABLES');
            $direction = 'INTO';
        }
        if ($reverse) {
            $from_db = env('DB_VOTER_DATABASE');
            $to_db = env('DB_DATABASE');
            $tables = DB::connection('voters')->select('SHOW TABLES');
            $direction = 'OUT OF';
        }

        foreach ($tables as $table) {
            foreach ($table as $key => $table_name) {
                if (
                    (in_array($table_name, $included)) ||
                    (in_array(substr($table_name, 0, 2), $included_prefixes))
                    ) {
                    if (! in_array($table_name, $excluded)) {
                        DB::statement('RENAME TABLE '.$from_db.'.'.$table_name.' TO '.$to_db.'.'.$table_name);

                        echo ' moved '.$direction.' holding pen: '.$table_name." \r\n";
                    }
                }
            }
        }
    }
}
