<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\VoterMaster;
use App\Municipality;

class FixWardsPrecincts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:fix_wards_precincts {--city=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes ward 0, finds and fixes bad wards precincts';

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
        $city = $this->option('city');
        if (!$city) {
            echo "Setting ward 0 to ward null for all records.";
            $voters = VoterMaster::where('ward', '0')
                             ->update(['ward' => null]);
            dd("fixed $voters, can also do like cf:fix_wards_precincts --city=belchertown");
        }
        $city_code = null;
        $city_obj = Municipality::where('name', 'LIKE', $city)->first();
        if (!$city_obj) {
            dd("BAD city ".$city);
        }
        $city_code = $city_obj->code;
        if (!$city_code) {
            dd("Code no good");
        }

        $voters = VoterMaster::where('city_code', $city_code)
                             ->where('ward', 0)
                             ->update(['ward' => null]);
        if ($city_code == 24) {
            // BELCHERTOWN HAS NO WARDS
            $voters = VoterMaster::where('city_code', $city_code)
                             ->where('ward', '>', 0)
                             ->update(['ward' => null]);
        }
        dd($voters);
    }
}
