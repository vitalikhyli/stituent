<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use DB;
use App\VoterMaster;

class CleanUpStoughton extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:stoughton';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Accidentally wiped precinct data, want to fill district data';

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
        $lookup_query = DB::select('select id, precinct from x_MA_STATE where city_code = 285 AND precinct is not null');
        $lookup = collect($lookup_query)->keyBy('id');
        //dd(collect($lookup)->keyBy('id'));
        $count = 0;
        $voters = VoterMaster::where('city_code', 285)->whereNull('precinct')->get();
        echo "\tCOUNT: ".$voters->count()."\n";
        foreach ($voters as $voter) {
            if (isset($lookup[$voter->id])) {
                $voter->precinct = $lookup[$voter->id]->precinct;
                //dd($voter);
                $voter->save();
                echo "\t".($count++)."\r";
            }
        }

        $voters = VoterMaster::where('city_code', 285)->whereNull('house_district')->get();
        echo "\tCOUNT: ".$voters->count()."\n";
        foreach ($voters as $voter) {
            if (   $voter->precinct == 2
                || $voter->precinct == 3
                || $voter->precinct == 4
                || $voter->precinct == 6) {
                $voter->house_district = 164;
                $voter->save();
            }

            if (   $voter->precinct == 1
                || $voter->precinct == 5
                || $voter->precinct == 7
                || $voter->precinct == 8) {
                $voter->house_district = 162;
                $voter->save();
            }
            echo "\t".($count++)."\r";
        }

        $voters = VoterMaster::where('city_code', 285)->whereNull('senate_district')->get();
        echo "\tCOUNT: ".$voters->count()."\n";
        foreach ($voters as $voter) {

            $voter->senate_district = 44;
            $voter->save();
 
            echo "\t".($count++)."\r";
        }
        return Command::SUCCESS;
    }
}
