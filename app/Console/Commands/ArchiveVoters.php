<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ArchiveVoters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:archive {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the updated_at and archives everything before a certain date';

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
        $archive_date = '2018-11-24';

        $this->archiveMaster($archive_date);

        if ($this->option('all')) {
            $tables = DB::select("SHOW TABLES LIKE 'x_MA_%'");
            foreach ($tables as $table) {
                foreach ($table as $key => $tablename) {
                    if (Str::contains($tablename, '_hh')) {
                        continue;
                    }
                    $this->archiveTable($tablename, $archive_date);
                }
            }
        }
    }

    public function archiveMaster($archive_date)
    {
        echo date('Y-m-d h:i:s', time()).' - Archiving Master before '.$archive_date."\n";
        DB::connection('voters')->table('x_voters_MA_master')
                                ->where('updated_at', '<', $archive_date)
                                ->whereNull('archived_at')
                                ->update(['archived_at' => date('Y-m-d')]);
        DB::connection('voters')->table('x_voters_MA_master')
                                ->whereNull('updated_at')
                                ->whereNull('archived_at')
                                ->update(['archived_at' => date('Y-m-d')]);
    }

    public function archiveTable($tablename, $archive_date)
    {
        //dd($index_arr);
        echo date('Y-m-d h:i:s', time()).' - Archiving '.$tablename.' before '.$archive_date."\n";
        DB::table($tablename)->where('updated_at', '<', $archive_date)
                             ->whereNull('archived_at')
                             ->update(['archived_at' => date('Y-m-d')]);
        DB::table($tablename)->whereNull('updated_at')
                             ->whereNull('archived_at')
                             ->update(['archived_at' => date('Y-m-d')]);
    }
}
