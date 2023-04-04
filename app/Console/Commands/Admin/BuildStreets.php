<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;
use DB;
use App\Street;
use App\Municipality;
use App\VoterMaster;
use Carbon\Carbon;
use App\MassGIS;

class BuildStreets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:build_streets {--clear} {--continue} {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds the streets table out of the household_ids, adds some boundaries.';

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
        if ($this->option('clear')) {

            Street::truncate();

            $streets = DB::connection('voters')
                         ->select('SELECT SUBSTRING(household_id, 1, 40) as name
                                        FROM x_voters_MA_master
                                        GROUP BY name
                                        ORDER BY name');
            
            //dd($streets[0]);
            $total = 0;
            $streets_arr = [];

            for ($i=0; $i<count($streets); $i++) {
                
                $street = $streets[$i];
                $s = [];
                if (stripos($street->name, 'deleted')) {
                    continue;
                }
                if (!$street->name) {
                    continue;
                }
                $s['id'] = $street->name;
                $streets_arr[$i] = $s;
                
                if ($total % 1000 == 0) {
                    Street::insert($streets_arr);
                    $streets_arr = [];
                }
                $total++;
            }
            Street::insert($streets_arr);
        }

        $total = 0;

        DB::connection('voters')
                  ->statement("UPDATE x_voters_MA_master 
                                SET household_id = CONCAT('MA', household_id)
                                WHERE household_id LIKE '|%'");

        $street_query = Street::query();
        if ($this->option('continue')) {
            $street_query->whereNull('city_code');
        }
        if ($this->option('update')) {
            $street_query->whereNull('lat_min');
        }
        $street_query->chunkById(1000, function($streets) use (&$total) {
            //dd($streets);
            foreach ($streets as $street) {


                //dd($street);
                $voters = VoterMaster::where('household_id', 'LIKE', $street->id.'%')
                                     ->whereNull('gis_outlier_at')
                                     ->get();

                echo "\t".$total."\t".$street->id." ".$voters->count()."\r";

                if ($voters->count() > 0) {

                    $household_count = $voters->groupBy('household_id')->count();
                    $lat_count = $voters->where('address_lat', '>', 0)->groupBy('address_lat')->count();

                    $city_arr = [];

                    $lat_median = $voters->where('address_lat', '>', 0)->median('address_lat');
                    $lon_median = $voters->where('address_long', '<', 0)->median('address_long');

                    
                    $outliers = [];

                    $voters_dupe = $voters->where('address_lat', '>', 0);

                    foreach ($voters as $index => $voter) {
                        if (isset($city_arr[$voter->address_city])) {
                            $city_arr[$voter->address_city] += 1;
                        } else {
                            $city_arr[$voter->address_city] = 1;
                        }

                        if ($lat_count > 2) {
                            if ($voter->address_lat > 0 && $voter->address_long < 0) {
                                $distance = abs($voter->address_lat - $lat_median) + abs($voter->address_long - $lon_median);
                                //echo $distance."\n";
                                $outlier = false;

                                if ($distance > .01) {
                                    // kinda far away from median
                                    $outlier = true;
                                    foreach ($voters_dupe as $voter_dupe) {
                                        if ($voter_dupe->id != $voter->id) {
                                            $temp_distance = abs($voter->address_lat - $voter_dupe->address_lat) + abs($voter->address_long - $voter_dupe->address_long);
                                            //echo "TEMP: ".$temp_distance."\n";
                                            if ($temp_distance < .01) {
                                                // has another close, not true outlier
                                                $outlier = false;
                                                break;
                                            }
                                        }
                                    }
                                    //dd($voter, $street, $distance, $voter->address_lat, $voter->address_long);

                                }
                                if ($outlier) {
                                    echo "OUTLIER: ".$voter->id." ".$street->id."\n";

                                    $voter->gis_outlier_at = Carbon::now();
                                    $voter->save();
                                }
                            }
                        }
                    }

                    if (count($outliers) > 0) {
                        //dd($outliers);
                    }
                    
                    rsort($city_arr);
                    $city = array_key_first($city_arr);
                    //dd($city_arr, $street->id);

                    echo "\t".$total."\t".$street->id." ".$voters->count()."\r";
                    $street->name = $voters->first()->address_street;
                    $street->city_code = $voters->first()->city_code;
                    $street->city = $city;
                    $street->voter_count = $voters->count();
                    $street->house_count = $household_count;
                    $street->lat_min = $voters->where('address_lat', '>', 0)->min('address_lat');
                    $street->lat_max = $voters->where('address_lat', '>', 0)->max('address_lat');
                    $street->long_min = $voters->where('address_long', '<', 0)->min('address_long');
                    $street->long_max = $voters->where('address_long', '<', 0)->max('address_long');
                    $street->min_num = (int) filter_var($voters->min('address_number'), FILTER_SANITIZE_NUMBER_INT);
                    $street->max_num = (int) filter_var($voters->max('address_number'), FILTER_SANITIZE_NUMBER_INT);

                    if ($street->city_code && !$street->city) {
                        $city_obj = Municipality::find($street->city_code);
                        if ($city_obj) {
                            $street->city = $city_obj->name;
                        }
                    }
                    //$street->created_at = Carbon::now();
                    $street->save();
                    
                } else {
                    echo "FAIL\n";
                }
                $total++;
            }
        });
        echo $total;
    }
}
