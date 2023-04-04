<?php

namespace App\Console\Commands\Admin;

use DB;
use Illuminate\Console\Command;
use Schema;

class RemoveHouseholdTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:remove_household_tables';

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
        $to_remove = [];

        foreach (DB::select('SHOW TABLES') as $table) {
            foreach ($table as $key => $table_name) {
                if (
                    (substr($table_name, 0, 2) == 'x_') &&
                    (substr($table_name, -3) == '_hh')
                ) {
                    $to_remove[] = $table_name;
                }
            }
        }

        echo "\r\n";
        $count = 1;
        foreach ($to_remove as $table_name) {
            echo $count."\t".$table_name."\r\n";
            $count++;
        }

        $confirm = $this->confirm('Do you want to remove these tables, chief?');

        if (! $confirm) {
            echo "Nothing done.\r\n";
        } else {
            echo "\r\n\r\n";
            $count = 1;
            foreach ($to_remove as $table_name) {
                try {
                    Schema::dropIfExists($table_name);
                    echo "Removed table \t".$count."\t".$table_name."             \r";
                    $count++;
                    usleep(500000);
                } catch (\Exception $e) {
                    echo $e;
                }
            }

            echo "\r\n\r\n";
            $this->info('Done');
        }
    }
}
