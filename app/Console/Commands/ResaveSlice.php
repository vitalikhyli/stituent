<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VoterSlice;
use App\VoterMaster;

class ResaveSlice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:resave_slice {--slice=}';

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
     * @return int
     */
    public function handle()
    {
        $slicename = $this->option('slice');
        if (!$slicename) {
            dd("Forgot slice dummy");
        }
        $slice = VoterSlice::where('name', $slicename)->first();
        if (!$slice) {
            dd("Invalid slice dummy");
        }
        $sql = $slice->sql;

        $count = 0;
        VoterMaster::whereRaw($sql)->chunkByID(1000, function($voters) use (&$count) {
            foreach ($voters as $voter) {
                //dd($voter);
                $voter->save();
                echo "DONE: ".($count++)."\r";
            }
        });
        echo "\nSaved $count voter records.";
    }
}
