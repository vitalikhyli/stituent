<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use App\VoterSlice;
use App\VoterMaster;

class FixBirthdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fix_birthdates {--slice=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes the birthdates data';

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
            if ($master->dob) {
                if ($master->dob > '2010-01-01') {
                    $date = $master->dob;
                    $date->subYear(100);
                    $master->dob = $date;
                    $master->save();
                    echo "Fixed dob for ".$master->id." to ".$date->format('Y-m-d')."\n";
                }
            }
            $count++;
            echo $count."\r";
        }
    }
}


