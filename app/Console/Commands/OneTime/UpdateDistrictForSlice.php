<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use App\VoterSlice;
use DB;

class UpdateDistrictForSlice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:update_district_for_slice {--slice=} {--district=} {--code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets all voters in a slice to a single district';

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
        $slicestr = $this->option('slice');
        if (!$slicestr) {
            dd("Add --slice= dummy");
        }
        $dist = $this->option('district');
        if (!$dist) {
            dd("Add --district= dummy");
        }
        $dist_id = $this->option('code');
        if (!$dist_id) {
            dd("Add --code= dummy");
        }

        $slice = VoterSlice::where('name', $slicestr)->first();
        if (!$slice) {
            dd("Bad slice");
        }
        $count = DB::table($slice->name)->count();

        if (!$this->confirm('Count: '.number_format($count).'. Do you wish to continue?')) {
            dd("Aborted");
        }

        $field = null;
        if ($this->option('district') == 'h') {
            $field = 'house_district';
        }
        if ($this->option('district') == 's') {
            $field = 'senate_district';
        }
        if (!$field) {
            dd("Bad district");
        }

        if (!$this->confirm('About to update '.$slice->name.' to '.$field.' = '.$dist_id.'. Do you wish to continue?')) {
            dd("Aborted");
        }

        DB::table($slice->name)->update([$field => $dist_id]);
        
        return Command::SUCCESS;
    }
}
