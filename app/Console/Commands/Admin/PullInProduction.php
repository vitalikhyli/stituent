<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;

class PullInProduction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:pull_in_production {--db} {--download} {--unzip} {--mount} {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls in production database and loads it locally.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $log_file;

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
        //return;
        // ==================================================> MYSQL DATABASE

        if (! file_exists(storage_path().'/sqldumps')) {
            mkdir(storage_path().'/sqldumps');
        }

        if ($this->option('db')) {


            // ===============> DOWNLOAD
            if ($this->option('download')) {

                $this->addToLog(date('Y-m-d-hia').": Downloading Tables...\n");
                shell_exec('scp forge@communityfluency.com:/home/forge/communityfluency.com/storage/sqldumps/cf_production_latest.zip '.storage_path().'/sqldumps/cf_production_latest.zip');
            }

            if ($this->option('unzip')) {
                $this->addToLog(date('Y-m-d-hia').": Unzipping Tables...\n");
                shell_exec('unzip -j '.storage_path().'/sqldumps/cf_production_latest.zip -d '.storage_path().'/sqldumps/');
            }
            
            // ===============> PUT INTO DB
            if ($this->option('mount')) {

                $this->addToLog(date('Y-m-d-hia').": Populating To DB...\n");
                $db = 'mysql -u '.config('app.db_username').' --password='.config('app.db_password').' fluency_base < '.storage_path().'/sqldumps/cf_production_latest.sql';
                $this->addToLog($db."\n");
                shell_exec($db);
            }

            // ===============> REMOVE SQL FILES
            if ($this->option('remove')) {

                $this->addToLog(date('Y-m-d-hia').": Removing sql file...\n");
                shell_exec('rm '.storage_path().'/sqldumps/cf_production_latest.sql');
            }
            // ================> DONE

            $this->addToLog(date('Y-m-d-hia').": Done.\n");
        }


    }

    public function addToLog($str)
    {
        echo $str;
    }
}
