<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VoterSlice;
use Illuminate\Support\Facades\Schema;
use DB;

class RecountSlices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:recount_slices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through each slice, recounts without archived.';

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
        $slices = VoterSlice::all();
        foreach ($slices as $slice) {
            $table = $slice->name;

            if (Schema::hasTable($table)) {
                $voters_count = DB::table($table)->count();
                $unarchived_count = DB::table($table)->whereNull('archived_at')->count();
                $hh_count = DB::table($table)->count(DB::raw('DISTINCT(household_id)'));
                //dd($voters_count, $unarchived_count);
                $slice->voters_count = $voters_count;
                $slice->unarchived_count = $unarchived_count;
                $slice->hh_count = $hh_count;

                //dd($slice);
                echo "Saving ".$table."\n";
                $slice->save();
            }
        }
    }
}






