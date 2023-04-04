<?php

namespace App\Console\Commands;

use App\Person;
use App\VoterMaster;
use Geocodio;
use Illuminate\Console\Command;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class GeocodeHouseholds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:geocode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets the geocode for X households with no valid geocoding.';

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

        try {

            /*
            $vms = VoterMaster::where('address_lat', '<', 1)
                              ->take(100)
                              ->get();
            foreach ($vms as $vm) {
                $response = Geocodio::geocode($vm->full_address);

                if (isset($response->results[0])) {
                    $address = $response->results[0]->address_components;
                    $location = $response->results[0]->location;

                    $matching_households = VoterMaster::where('household_id', $vm->household_id)->get();
                    foreach ($matching_households as $hh) {
                        if (isset($address->zip)) {
                            $hh->address_zip = $address->zip;
                        }
                        $hh->address_lat = $location->lat;
                        $hh->address_long = $location->lng;
                        $hh->location = new Point($hh->address_lat, $hh->address_long);
                        $hh->save();
                    }
                    echo date('Y-m-d h:i:a').' Geocoded '.$matching_households->count().' at '.$vm->full_address."\n";
                }
            }
            */
            //return;

            $peeps = Person::whereNotNull('address_number')
                           ->whereNotNull('address_street')
                           ->whereNotNull('address_city')
                           ->whereNull('address_lat')
                           ->take(100)
                           //->inRandomOrder()
                           ->orderBy('updated_at', 'desc')
                           ->get();

            echo 'About to geocode '.$peeps->count()." People.\n";

            foreach ($peeps as $vm) {
                $response = Geocodio::geocode($vm->full_address);

                if (isset($response->results[0])) {
                    $address = $response->results[0]->address_components;
                    $location = $response->results[0]->location;

                    $matching_households = Person::where('address_number', $vm->address_number)
                                                 ->where('address_street', $vm->address_street)
                                                 ->where('address_city', $vm->address_city)
                                                 ->where('address_state', $vm->address_state)
                                                 ->get();
                    foreach ($matching_households as $hh) {
                        if (isset($address->zip)) {
                            $hh->address_zip = $address->zip;
                            $hh->address_lat = $location->lat;
                            $hh->address_long = $location->lng;
                            $hh->save();
                        } else {
                            echo 'DUD ADDRESS? '.$vm->full_address."\r\n";
                        }
                    }
                    echo date('Y-m-d h:i:a').' Geocoded '.$matching_households->count().' at '.$vm->full_address."\n";
                }
            }

        } catch (\Exception $e) {

            // Do not fill up log with errors which are frequent

        }
    }
}
