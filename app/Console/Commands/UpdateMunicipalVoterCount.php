<?php

namespace App\Console\Commands;

use App\Municipality;
use App\VoterMaster;
use Illuminate\Console\Command;

class UpdateMunicipalVoterCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:municipal_voter_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks how many records we have for each city and saves it.';

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
        //dd(VoterMaster::pluck('city_code'));
        foreach (Municipality::orderBy('name')->get() as $municipality) {
            echo $municipality->code.' - '.$municipality->name.': ';
            $voter_count = VoterMaster::where('city_code', $municipality->code)
                                ->count();
            $municipality->voter_count = $voter_count;
            $municipality->save();
            echo "$voter_count Saved!\n";
        }
    }
}
