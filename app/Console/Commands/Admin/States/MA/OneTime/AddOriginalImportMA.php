<?php

namespace App\Console\Commands\Admin\States\MA\OneTime;

use App\Console\Commands\Admin\States\NationalMaster;

use App\VoterMaster;

use Carbon\Carbon;


class AddOriginalImportMA extends NationalMaster
{
    protected $signature                = 'cf:ma_original_import';
    protected $description              = '';
    public $state                       = 'MA';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $new = $this->getMaster($this->state);

        if (!$new) {
            $this->error('Problem selecting Master Table');
            dd('Exiting.');
        }

        echo $this->redLineMessage("This command will add original_import data to $new");
        $go = $this->confirm('Proceed?', true);

        if (!$go) dd('Exiting');

        ////////////////////////////////////////////////////////////////////////////////

        session(['team_state' => $this->state]); // Sets VoterMaster

        echo "Counting rows...\n";
        $this->expected_num_rows    = VoterMaster::count();
        $this->start_time           = Carbon::now();
        $log                        = $this->createErrorLog('add_original_import');
        $error_count                = 0;
        $chunk  = 250;
        $row    = 0;

        $launch_date = Carbon::parse('2019-12-01');

        VoterMaster::chunkById($chunk, function($voters) use (&$row, &$error_count, &$log, $launch_date) {

            foreach ($voters as $voter) {

                echo $this->progress($row++)."\r";
                
                try {

                    $data = collect($voter->getAttributes())->except('original_import');

                    if ($voter->created_at->lte($launch_date)) {
                        $key_date    = $launch_date->toDateString();
                        $default_key = 'LAUNCH';
                    } else {
                        $key_date = $voter->created_at->toDateString();
                        $default_key = 'IMPORT';
                    }

                    $key = (!$voter->origin_method) ? $default_key 
                                                : str_replace('_', '-', $voter->origin_method);

                    $voter->original_import = [$key_date.'_'.$key => $data];
                    $voter->save();

                } catch (\Exception $e) {

                    $log->error($e->getMessage());
                    $error_count++;
                    $this->error('Error id: '.$voter->id.' -- see log.');

                }

            }

        });
        
        ////////////////////////////////////////////////////////////////////////////////

        echo "\n".$this->blueLineMessage("Done.");

        if ($error_count >  0) {
            $this->info("$error_count errors in log.");
        }
        
    }

    //////////////////////////////////////////////////////////////////////////////////////////

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }

}
