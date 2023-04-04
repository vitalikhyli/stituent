<?php

namespace App\Console\Commands\CC;

use Illuminate\Console\Command;

class PullInCCData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:pull_in_cc_data {--db} {--all} {--files} {--download} {--unzip} {--mount} {--remove}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls in production database and loads it locally to the cc database.';

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

        if (! file_exists(storage_path().'/mnt/sqldumps')) {
            mkdir(storage_path().'/mnt/sqldumps');
        }

        $this->log_file = storage_path().'/mnt/sqldumps/log.txt';

        if ($this->option('db')) {

            /*
  mysqldump -u root -p!JiBoKri5! thebarre_cms cms_election_data > /root/db_backups/election_data.sql
  gzip -v /root/db_backups/election_data.sql

  mysqldump -u root -p!JiBoKri5! thebarre_cms cms_voters > /root/db_backups/voters.sql
  gzip -v /root/db_backups/voters.sql

  mysqldump -u root -p!JiBoKri5! thebarre_cms --ignore-table=thebarre_cms.cms_voters --ignore-table=thebarre_cms.cms_election_data --ignore-table=thebarre_cms.cms_voters_import > /root/db_backups/other_tables.sql
  gzip -v /root/db_backups/other_tables.sql


  */

            // ===============> DOWNLOAD
            if ($this->option('download')) {
                if ($this->option('all')) {
                    $this->addToLog(date('Y-m-d-hia').": Downloading Voters...\n");
                    shell_exec('scp root@198.57.216.17:/root/db_backups/voters.sql.gz '.storage_path().'/mnt/sqldumps/');

                    $this->addToLog(date('Y-m-d-hia').": Downloading Election Data...\n");
                    shell_exec('scp root@198.57.216.17:/root/db_backups/election_data.sql.gz '.storage_path().'/mnt/sqldumps/');
                }

                $this->addToLog(date('Y-m-d-hia').": Downloading Other Tables...\n");
                shell_exec('scp root@198.57.216.17:/root/db_backups/other_tables.sql.gz '.storage_path().'/mnt/sqldumps/');
            }
            // ===============> UNZIP
            if ($this->option('unzip')) {
                if ($this->option('all')) {
                    $this->addToLog(date('Y-m-d-hia').": Unzipping Voters...\n");
                    shell_exec('gunzip -f '.storage_path().'/mnt/sqldumps/voters.sql.gz');

                    $this->addToLog(date('Y-m-d-hia').": Unzipping Election Data...\n");
                    shell_exec('gunzip -f '.storage_path().'/mnt/sqldumps/election_data.sql.gz');
                }

                $this->addToLog(date('Y-m-d-hia').": Unzipping Other Tables...\n");
                shell_exec('gunzip -f '.storage_path().'/mnt/sqldumps/other_tables.sql.gz');
            }
            // ===============> PUT INTO DB
            if ($this->option('mount')) {
                if ($this->option('all')) {
                    $this->addToLog(date('Y-m-d-hia').": Populating Voters...\n");
                    $db = 'mysql -u '.config('app.db_username').' --password='.config('app.db_password').' ccdb < '.storage_path().'/mnt/sqldumps/voters.sql';
                    $this->addToLog($db."\n");
                    shell_exec($db);

                    $this->addToLog(date('Y-m-d-hia').": Populating Election Data...\n");
                    $db = 'mysql -u '.config('app.db_username').' --password='.config('app.db_password').' ccdb < '.storage_path().'/mnt/sqldumps/election_data.sql';
                    $this->addToLog($db."\n");
                    shell_exec($db);
                }

                $this->addToLog(date('Y-m-d-hia').": Populating Other Tables...\n");
                $db = 'mysql -u '.config('app.db_username').' --password='.config('app.db_password').' ccdb < '.storage_path().'/mnt/sqldumps/other_tables.sql';
                $this->addToLog($db."\n");
                shell_exec($db);
            }

            // ===============> REMOVE SQL FILES
            if ($this->option('remove')) {
                if ($this->option('all')) {
                    $this->addToLog(date('Y-m-d-hia').": Removing sql file...\n");
                    shell_exec('rm '.storage_path().'/mnt/sqldumps/voters.sql');

                    $this->addToLog(date('Y-m-d-hia').": Removing sql file...\n");
                    shell_exec('rm '.storage_path().'/mnt/sqldumps/election_data.sql');
                }

                $this->addToLog(date('Y-m-d-hia').": Removing sql file...\n");
                shell_exec('rm '.storage_path().'/mnt/sqldumps/other_tables.sql');
            }
            // ================> DONE

            $this->addToLog(date('Y-m-d-hia').": Done.\n");
        }

        // voter_code
        // election_code
        // other_fields -> json

        // ======================================================> ALL FILES FROM CC
        if ($this->option('files')) {
            if ($this->option('download')) {
                $this->addToLog(date('Y-m-d-hia').": Downloading files...\n");
                shell_exec('scp root@198.57.216.17:/root/db_backups/files.tar.gz '.storage_path().'/mnt/sqldumps/files.tar.gz');
            }

            $this->addToLog(date('Y-m-d-hia').": Extracting files...\n");
            shell_exec('tar xf '.storage_path().'/mnt/sqldumps/files.tar.gz -C '.storage_path().'/mnt/cc_files/');
        }
    }

    public function addToLog($str)
    {
        echo $str;
        file_put_contents(storage_path().'/mnt/sqldumps/log.txt', $str, FILE_APPEND);
    }
}
