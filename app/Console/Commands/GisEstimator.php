<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\VoterMaster;
use App\Street;
use DB;
use Carbon\Carbon;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class GisEstimator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:gis_estimator {--start=} 
                                             {--clear} 
                                             {--db} 
                                             {--not_null} 
                                             {--correct_latlong}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates db (adds gis_estimated_at, gis_outlier_at), loops through all to get estimated GIS';

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
        $master_voter_table = 'x_voters_MA_master';
        $current_columns = Schema::connection('voters')
                                 ->getColumnListing($master_voter_table);

        if ($this->option('db')) {
          if (!in_array('location', $current_columns)) {
              Schema::connection('voters')->table($master_voter_table, function (Blueprint $table) {
                  echo "Adding location\n";
                  $table->point('location')->after('address_long')->nullable();
              });
          }
          if (!in_array('gis_estimated_at', $current_columns)) {
              Schema::connection('voters')->table($master_voter_table, function (Blueprint $table) {
                  echo "Adding gis_estimated_at\n";
                  $table->dateTime('gis_estimated_at')->after('location')->nullable();
              });
          }

          if (!in_array('gis_outlier_at', $current_columns)) {
              Schema::connection('voters')->table($master_voter_table, function (Blueprint $table) {
                  echo "Adding gis_outlier_at\n";
                  $table->dateTime('gis_outlier_at')->after('gis_estimated_at')->nullable();
              });
          }
          return;
        }

        if ($this->option('correct_latlong')) {
          $distance_sql = "ST_Distance(`location`, ST_GeomFromText( 'POINT(-71 42)' )) > 5";
          $count = VoterMaster::whereNotNull('address_lat')
                            ->where('address_lat', '>', 0)
                            ->whereRaw($distance_sql)
                            ->count();
                            //dd($count);

          echo "Count: $count\n";
          $i=0;
          $start_time = microtime(-1);
          
          VoterMaster::whereNotNull('address_lat')
                            ->where('address_lat', '>', 0)
                            ->whereRaw($distance_sql)
                            ->chunkById(10000, function ($voters) use (&$i, $count) {
            echo "\t\t$i (".round(($i/$count)*100, 1)."%)\r";
            VoterMaster::whereIn('id', $voters->pluck('id'))
                       ->update(['location' => DB::raw('POINT(address_long, address_lat)')]);

          });
          $start_time = microtime(-1);
          
          return;
        }

        if ($this->option('clear')) {
            DB::connection('voters')
                      ->statement("UPDATE $master_voter_table 
                                    SET location = POINT(0,0)");
        }
        if ($this->option('not_null')) {
            echo "Counting null locations\n";
          $count = VoterMaster::whereNull('location')
                            ->count();
            echo "Setting location to 0,0 for null for $count\n";

            $i = 0;
            while ($i < $count + 100000) {
              DB::connection('voters')
                ->statement("UPDATE $master_voter_table SET `location` = POINT(address_long,address_lat)
                               where location is null
                               AND address_lat > 0
                               LIMIT 100000");
                $i += 100000;
                echo "\t\t$i Done \r";
            }

            $i = 0;
            while ($i < $count + 100000) {
              DB::connection('voters')
                ->statement("UPDATE $master_voter_table SET `location` = POINT(0,0)
                               where location is null
                               LIMIT 100000");
                $i += 100000;
                echo "\t\t$i Done \r";
            }

            echo "Changing column to not null\n";
            DB::connection('voters')
              ->statement("ALTER TABLE $master_voter_table CHANGE `location` `location` POINT NOT NULL;");

          return;
        }

        $count = VoterMaster::whereNotNull('address_lat')
                            ->where('address_lat', '>', 0)
                            ->where('location', 'POINT(0,0)')
                            ->count();

        echo "Count: $count\n";
        $start_time = microtime(-1);
        for ($i=0; $i<(2*$count); $i+=100000) {
            $time_per_100000 = (microtime(-1) - $start_time) / 100000;
            echo "\t\t$i (".round(($i/$count)*100, 1)."%)\t\t$time_per_100000\r";
            DB::connection('voters')
                  ->statement("UPDATE $master_voter_table 
                                SET location = POINT(address_long, address_lat)
                                WHERE location = POINT(0,0)
                                AND address_lat > 0
                                LIMIT 100000");
        }
        
        
        DB::connection('voters')
                  ->statement("UPDATE $master_voter_table 
                                SET household_id = CONCAT('MA', household_id)
                                WHERE household_id LIKE '|%'");

        $i = 0;
        $estimated_count = 0;

        $street_query = Street::query();
        if ($this->option('start')) {
          $street_query->where('id', '>=', $this->option('start'));
        }

        $street_query->chunkById(1000, function($streets) use (&$i, &$estimated_count) {

            foreach ($streets as $street) {
                $i++;
                $voters = $street->voters()->get();

                if ($voters->count() > 0) {

                    $missing = collect([]);
                    foreach ($voters as $voter) {
                      if ($voter->address_lat < 1) {
                        $missing[] = $voter;
                      }
                    }
                    //dd($voters, $street);
                    $voter_count = $voters->count();
                    $house_count = $voters->groupBy('household_id')->count();
                    $missing_count = $missing->count();

                   
                    //echo $i.":\t".$street->id."\t".$voters->count()."\t".$missing_count."\n";

                    if ($missing_count > 0) {
                        echo $i.":\t".$street->id."\t".$voters->count()."\t".$missing_count."\t";
                        
                        if ($voter_count > $missing_count + 1) {
                            echo "GONNA ESTIMATE, ";
                            echo "ESTIMATED COUNT: $estimated_count.\n";
                            $by_num = [];
                            $valid_nums = [];
                            $blank_nums = [];
                            foreach ($voters as $vkey => $voter) {
                              $by_num[$voter->address_number][$voter->address_lat][$vkey] = 1;
                              if ($voter->address_lat > 0) {
                                $valid_nums[$voter->address_number] = $voter;
                              } else {
                                $blank_nums[$voter->address_number][] = $voter;
                              }
                            }
                            if (count($valid_nums) < 2) {
                              echo "ONLY ".count($valid_nums)." VALID NUMS, CAN'T ESTIMATE\n";
                              continue;
                            }
                            foreach ($blank_nums as $housenum => $blank_keys) {

                              if (isset($valid_nums[$housenum])) {
                                echo "\t\t HOUSE ALREADY VALID!\n";
                                foreach ($blank_keys as $address_num => $blank_voter) {
                                  $valid_voter = $valid_nums[$housenum];
                                  $blank_voter->address_lat = $valid_voter->address_lat;
                                  $blank_voter->address_long = $valid_voter->address_long;
                                  $blank_voter->location = $valid_voter->location;
                                  $blank_voter->gis_estimated_at = Carbon::now();
                                  //dd($blank_voter);
                                  echo "\t\tUPDATING ".$blank_voter->id."\n";
                                  $blank_voter->save();
                                  $estimated_count++;
                                }
                              } else {
                                echo "\t\t CHECKING NEIGHBORS...\n";
                                $valid_prev = null;
                                $valid_next = null;

                                foreach ($valid_nums as $vnum => $vvoter) {
                                  if ($vnum > $housenum) {
                                    $valid_next = $vvoter;
                                    break;
                                  }
                                  if ($vnum < $housenum) {
                                    $valid_prev = $vvoter;
                                  }
                                }
                                if ($valid_prev && $valid_next) {

                                  foreach ($blank_keys as $address_num => $blank_voter) {
                                    $estimated_lat = ($valid_prev->address_lat + $valid_next->address_lat) / 2;
                                    $estimated_long = ($valid_prev->address_long + $valid_next->address_long) / 2;

                                    

                                    

                                    //dd($valid_prev->address_lat, $estimated_point);

                                    $blank_voter->address_lat = $estimated_lat;
                                    $blank_voter->address_long = $estimated_long;

                                    // FUCKING BACKWARDS?????? WHY??
                                    $estimated_point = new Point($estimated_long, $estimated_lat); 

                                    $blank_voter->location = $estimated_point;
                                    $blank_voter->gis_estimated_at = Carbon::now();
                                    //dd($blank_voter);
                                    echo "\t\tUPDATING ".$blank_voter->id."\n";
                                    //dd($blank_voter);
                                    $blank_voter->save();
                                    $estimated_count++;
                                  }

                                }
                              }

                            }
                            
                        } else {
                            echo "NO DATA\n";
                        }
                    }
                }
            }
        });
        


        
    }
}
