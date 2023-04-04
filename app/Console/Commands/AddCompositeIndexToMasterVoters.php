<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddCompositeIndexToMasterVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:composite {--all} {--people} {--state=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds Indexes most likely used for Constituents Search page';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public $state;
    public $table;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->table = 'x_voters_MA_master';
        $this->state = 'MA';
        if ($this->option('state')) {
            $this->table = 'x_voters_'.$this->option('state').'_master';
            $this->state = $this->option('state');
        }
        
        $indexes = [

            ['address_city', 'last_name', 'first_name'], // Lookup on User Uploads

            ['first_name', 'last_name'],
            ['first_name', 'city_code'],
            ['first_name', 'address_street'],
            ['first_name', 'address_zip'],
            ['first_name', 'party'],
            ['first_name', 'dob'],

            ['last_name', 'city_code'],
            ['last_name', 'address_street'],
            ['last_name', 'address_zip'],
            ['last_name', 'party'],
            ['last_name', 'dob'],
            // ['last_name', 'household_id'],

            ['city_code', 'address_street'],
            ['city_code', 'address_zip'],
            ['city_code', 'party'],
            ['city_code', 'dob'],               // LOW CARDINALITY --> WORTH IT?
            ['city_code', 'ward', 'precinct'],  // LOW CARDINALITY --> WORTH IT?

            ['address_number', 'address_street'],

            ['address_street', 'address_zip'],  // LOW CARDINALITY --> WORTH IT?
            ['address_street', 'party'],
            ['address_street', 'dob'],

            ['address_zip', 'party'],           // LOW CARDINALITY --> WORTH IT?
            ['address_zip', 'dob'],             // LOW CARDINALITY --> WORTH IT?

            ['party', 'dob'],                   // LOW CARDINALITY --> WORTH IT?

            ['archived_at', 'updated_at'],
            ['archived_at', 'deceased'],

        ];

        foreach ($indexes as $index_arr) {
            $this->addIndexToMaster($index_arr);
        }


        if ($this->option('all')) {
            $tables = DB::select("SHOW TABLES LIKE 'x_".$this->state."_%'");
            foreach ($tables as $table) {
                foreach ($table as $key => $tablename) {
                    if (Str::contains($tablename, '_hh')) {
                        continue;
                    }
                    foreach ($indexes as $index_arr) {
                        $this->addIndexToTable($tablename, $index_arr);
                    }
                }
            }
        }
        if ($this->option('people')) {
            // has no archived_at
            // and every index starts with the team
            foreach ($indexes as $index_arr) {
                if (! in_array('archived_at', $index_arr)) {
                    $peopleindex = ['team_id'];
                    foreach ($index_arr as $index) {
                        $peopleindex[] = $index;
                    }
                    $this->addIndexToTable('people', $peopleindex);
                }
            }
        }
    }

    public function addIndexToMaster($index_arr)
    {
        echo date('Y-m-d h:i:s', time()).' - Adding Master index for '.implode(', ', $index_arr);
        Schema::connection('voters')->table($this->table, function (Blueprint $table) use ($index_arr) {
            $index_name = 'idx_master_'.implode('_', $index_arr);
            $existing_indexes = collect(DB::connection('voters')
                                          ->select('SHOW INDEXES FROM '.$this->table))
                                    ->pluck('Key_name');
            //dd($existing_indexes);
            if ($existing_indexes->contains($index_name)) {
                echo " => $index_name exists, skipping.\n";

                return;
            } else {
                echo "\n";
            }
            $table->index($index_arr, $index_name);
        });
    }

    public function addIndexToTable($tablename, $index_arr)
    {
        //dd($index_arr);
        echo date('Y-m-d h:i:s', time())." - $tablename: ".implode(', ', $index_arr);
        Schema::table($tablename, function (Blueprint $table) use ($index_arr, $tablename) {
            $index_name = 'idx_master_'.implode('_', $index_arr);
            $existing_indexes = collect(DB::select('SHOW INDEXES FROM '.$tablename))
                                    ->pluck('Key_name');
            //dd($existing_indexes);
            if ($existing_indexes->contains($index_name)) {
                echo " => Exists, SKIPPING.\n";

                return;
            } else {
                echo "\n";
            }
            $table->index($index_arr, $index_name);
        });
    }
}
