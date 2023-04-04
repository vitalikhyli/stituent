<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

Use App\Models\ImportedVoterMaster;

use Carbon\Carbon;


class FixCreatedAt extends NationalMaster
{
    protected $signature = 'cf:created_at_ma';
    protected $description = '';

    public $state = 'MA';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $table = $this->selectPreviousMaster();
        session(['table_while_importing_master' => $table]);

        $i          = 1;
        $successes  = 0;
        $errors     = 0;
        $chunk      = 2500;

        //dd(ImportedVoterMaster::whereNull('created_at')->withTrashed()->toSql());
        $this->expected_num_rows    = ImportedVoterMaster::whereNull('created_at')->withTrashed()->count();
        $this->start_time           = Carbon::now();

        $this->info ("Expected Count: $this->expected_num_rows");

        while ($i < $this->expected_num_rows) {

            $voters = ImportedVoterMaster::withTrashed()
                                         ->whereNull('created_at')
                                         ->take($chunk)
                                         ->update(['created_at' => $this->start_time]);

            echo $this->progress($i += $chunk)."\r";

            // foreach ($voters as $voter) {

            //     if (!$voter->created_at) {

            //         try {

            //             $voter->created_at = $this->start_time;

            //             $voter->save();

            //             $successes++;

            //         } catch (\Exception $e) {

            //             $this->error($e->getMessage());
            //             $errors++;

            //         }

            //     }

            // }

        }

        echo $this->basicLine();
        // $this->info("Successes: $successes");
        // $this->info("Errors:    $errors");
        // echo "\n";
    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
}
