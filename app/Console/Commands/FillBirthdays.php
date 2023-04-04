<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use DB;

class FillBirthdays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fill_birthdays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through every table and sets the new birthday field';

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
        // Voter master
        if (Schema::connection('voters')->hasColumn('x_voters_MA_master', 'birthday')) {
            echo date('Y-m-d h:i:s')." adding x_voters_MA_master birthday\n";
            $this->crushByTheGrand('x_voters_MA_master', 'voters');
        }

        // People
        if (Schema::hasColumn('people', 'birthday')) {
            echo date('Y-m-d h:i:s')." adding people birthday\n";
            $this->crushByTheGrand('people', 'main');
        }

        $table_query = DB::select("SHOW TABLES LIKE 'x_MA_%'");
        foreach ($table_query as $tableresults) {
            foreach ($tableresults as $key => $tablename) {
                if (! Schema::hasColumn($tablename, 'birthday')) {
                    echo $tablename." has no birthday field yet.\n";
                    continue;
                }
                if (Str::contains($tablename, '_hh')) {
                    continue;
                }
                $this->crushByTheGrand($tablename, 'main');
            }
        }
        return Command::SUCCESS;
    }
    public function crushByTheGrand($tablename, $connection)
    {
        $count = DB::connection($connection)->table($tablename)
                   ->whereNull('birthday')
                   ->whereNotNull('dob')
                   ->count();

        echo date('Y-m-d H:i:a').' '.$tablename.": $count left.\n";
        //dd($count);
        while ($count > 0) {
            $count = DB::connection($connection)->table($tablename)
                   ->whereNull('birthday')
                   ->whereNotNull('dob')
                   ->count();
            echo date('Y-m-d H:i:a').' '.$tablename.": $count left.\n";
            $update_str = "UPDATE $tablename 
                            SET birthday = DATE_FORMAT(dob, '%m-%d') 
                            WHERE birthday is null 
                            AND dob is not null
                            LIMIT 100000";
            //echo $update_str."\n";

            DB::connection($connection)->statement($update_str);
        }
    }
}
