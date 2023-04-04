<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use DB;

class CreateMySQLDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:create_mysql_dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dumps non-voter tables to storage/sqldumps/cf_production_X.sql';

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
        if (!file_exists(storage_path().'/sqldumps')) {
            mkdir(storage_path().'/sqldumps');
        }
        // store last X days
        $days_back = 7;
        // day of year mod X
        $backup_num = date('z', time()) % $days_back;
        // will rewrite every X days

        $table_col = 'Tables_in_'.env('DB_DATABASE');

        $tables_collection = DB::select(
                'SHOW TABLES from '.env('DB_DATABASE')." 
                   WHERE $table_col NOT LIKE 'x_%'");
        $tables = [];
        //print_r($tables_collection);
        foreach ($tables_collection as $key => $table_item) {
            if ($key > 3) {
                //continue;
            }
            $tables[] = $table_item->$table_col;
        }
        //dd($tables);
        $table_str = "";
        foreach ($tables as $table) {
            $table_str .= $table." ";
        }
        $table_str = trim($table_str);
        //echo $table_str;

        $filepath = storage_path().'/sqldumps/cf_production_'.$backup_num.'.sql';
        $zip_path = storage_path().'/sqldumps/cf_production_'.$backup_num.'.zip';
        $latest_path = storage_path().'/sqldumps/cf_production_latest.sql';
        $latest_zip_path = storage_path().'/sqldumps/cf_production_latest.zip';

        $command = 'mysqldump --single-transaction -u '.config('app.db_username').' -p'.config('app.db_password').' '.env('DB_DATABASE').' '.$table_str.' > '.$filepath;

        
        shell_exec($command);
        echo "Created ".$filepath."\n";

        shell_exec("zip -r $zip_path $filepath");
        echo "Zipped ".$filepath."\n";

        if (file_exists($filepath) && file_exists($zip_path)) {
            shell_exec("cp $filepath $latest_path");
            echo "Copied to Latest: ".$latest_path."\n";

            shell_exec("rm $filepath");
            echo "Removed ".$filepath."\n";

            shell_exec("zip -r $latest_zip_path $latest_path");
            echo "Zipped Latest ".$latest_zip_path."\n";

            shell_exec("rm $latest_path");
            echo "Removed ".$latest_path."\n";
        }
    }
}
