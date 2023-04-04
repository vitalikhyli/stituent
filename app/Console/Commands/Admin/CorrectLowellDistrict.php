<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use DB;
use App\VoterSlice;

class CorrectLowellDistrict extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:correct_lowell_district';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limits a specific district to ONLY the voters in a provided list from the campaign.';

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
        $voters_to_keep = DB::connection('imports')
                            ->table('x_lowell_2021_05_07_12_41_38')
                            ->pluck('id');

        echo "To keep: ".$voters_to_keep->count()."\n";

        $remove = DB::connection('main')
                    ->table('x_MA_LowellWards5And9')
                    ->whereNotIn('id', $voters_to_keep)
                    ->delete();

        $after = DB::connection('main')
                    ->table('x_MA_LowellWards5And9')
                    ->count();

        echo "After Count: ".$after."\n";

        $current = DB::connection('main')
                     ->table('x_MA_LowellWards5And9')
                     ->pluck('id');

        DB::connection('main')
          ->table('x_MA_LowellWards5And9')
          ->update(['archived_at' => null]);

        $voter_slice = VoterSlice::where('name', 'x_MA_LowellWards5And9')->first();
        $voter_slice->voters_count = $current->count();
        $voter_slice->unarchived_count = $current->count();
        $voter_slice->save();

        dd($current->diff($voters_to_keep));
    }
}
