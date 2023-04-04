<?php

namespace App\Console\Commands;

use App\Person;
use App\VoterMaster;
use Illuminate\Console\Command;

class RefreshGIS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:refresh_gis {--chunk=}';

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
     * @return mixed
     */
    public function handle()
    {
        echo date('Y-m-d h:i:s')." Starting\n";

        $chunk = 1000;
        if ($this->option('chunk')) {
            $chunk = $this->option('chunk');
        }

        $voters = VoterMaster::where('mass_gis_id', '>', 0)
                   ->where('address_lat', 'LIKE', '-%')
                   ->with('massGis')
                   ->take($chunk)
                   ->get();

        $count = 0;
        echo date('Y-m-d h:i:s').' Queried '.$voters->count()."\n";

        foreach ($voters as $voter) {
            $count++;
            $voter->address_lat = $voter->massGis->address_lat;
            $voter->address_long = $voter->massGis->address_long;
            //dd($voter);
            $voter->save();
            if ($count % 1000 == 0) {
                echo date('Y-m-d h:i:s').' Just fixed '.$voter->id."\n";
            }
        }

        $people = Person::where('address_lat', '<', '0')
                   ->take($chunk)
                   ->get();

        $count = 0;
        echo date('Y-m-d h:i:s').' Queried '.$people->count()." People\n";

        foreach ($people as $person) {
            $count++;
            // SWAP LAT/LONG
            $old_lat = $person->address_lat;
            $old_long = $person->address_long;
            $person->address_lat = $old_long;
            $person->address_long = $old_lat;
            //dd($person);
            $person->save();
            if ($count % 1000 == 0) {
                echo date('Y-m-d h:i:s').' Just fixed '.$person->id."\n";
            }
        }
    }
}
