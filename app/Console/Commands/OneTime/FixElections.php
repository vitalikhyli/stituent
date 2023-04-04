<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use App\VoterSlice;
use App\VoterMaster;

class FixElections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fix_elections {--slice=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes the format for elections data';

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
        if (!$this->option('slice')) {
            dd("Needs slice like php artisan cf:fix_elections --slice=x_MA_M_Holyoke");
        }
        $slice = VoterSlice::where('name', $this->option('slice'))->first();
        if (!$slice) {
            dd("Slice ".$this->option('slice')." not found");
        }
        //dd("Laz");
        $sql = $slice->sql;
        if ($sql == '1') {
            //dd("Laz");
            $masters = VoterMaster::take(10)->orderBy('id')->get();
        } else {
            $masters = VoterMaster::whereRaw($sql)->get();    
        }
        
        // dd($masters);
        // dd($masters);
        $count = 0;
        foreach ($masters as $master) {
            //dd($master);
            $elections = $master->elections;
            if (is_array($elections)) {
                //dd("Laz");
                foreach ($elections as $election_id => $val) {
                    //dd($election_id);
                    if (strpos($election_id, ' ')) {
                        echo "Fixing ".$master->id.", $election_id\n";
                        $newid = str_replace(' ', '', $election_id);
                        $elections[$newid] = $val;
                        unset($elections[$election_id]);
                        $master->elections = $elections;
                        $master->save();
                    }
                }
            }
            $count++;
            echo $count."\r";
        }
    }
}


