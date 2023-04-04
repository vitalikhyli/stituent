<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use Illuminate\Console\Command;
use App\Models\ImportedVoterMaster;
use Carbon\Carbon;

class FillInNullDistricts extends NationalMaster
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:ma_fill_in_null_districts';

    public $state = 'MA';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill in null districts';

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
        $this->new_master = $this->selectPreviousMaster();

        session(['table_while_importing_master' => $this->new_master]);
        session(['team_state' => $this->state]);


        $i=1;
        $this->expected_num_rows = ImportedVoterMaster::where('address_city', 'LIKE', 'Manchester%')
                                                      ->where('city_code', '!=', 166)
                                                      ->count();
        $this->start_time           = Carbon::now();
        echo "Manchester wrong code: ".$this->expected_num_rows."\n";
        ImportedVoterMaster::where('address_city', 'LIKE', 'Manchester%')
                           ->where('city_code', '!=', 166)
                           ->chunkById(500, function($records) use (&$i) {
            
            foreach ($records as $record) {
                echo $this->progress($i++)."\r";
                $record->city_code = 166;
                $record->precinct = 1;
                $record->save();
            }
        }); 

        $i=1;
        $this->expected_num_rows = ImportedVoterMaster::where('address_city', 'LIKE', 'Manchester%')
                                                      ->whereNull('city_code')
                                                      ->count();
        $this->start_time           = Carbon::now();
        echo "Manchester null city: ".$this->expected_num_rows."\n";
        ImportedVoterMaster::where('address_city', 'LIKE', 'Manchester%')
                           ->whereNull('city_code')
                           ->chunkById(500, function($records) use (&$i) {
            
            foreach ($records as $record) {
                echo $this->progress($i++)."\r";
                $record->city_code = 166;
                $record->precinct = 1;
                $record->save();
            }
        });        


        $i=1;
        $this->expected_num_rows = ImportedVoterMaster::whereNull('address_state')->count();
        $this->start_time           = Carbon::now();
        echo "Null State: ".$this->expected_num_rows."\n";
        ImportedVoterMaster::whereNull('address_state')
                           ->chunkById(500, function($null_states) use (&$i) {
            
            foreach ($null_states as $nullstate) {
                echo $this->progress($i++)."\r";
                $nullstate->address_state = $this->state;
                $nullstate->save();
            }
        });

        $codes = ['congress_district', 'city_code', 'house_district', 'senate_district', 'county_code'];

        foreach ($codes as $code) {

            $this->expected_num_rows = ImportedVoterMaster::where($code, '<', 1)->count();
            $this->start_time           = Carbon::now();
            echo strtoupper($code)." Expected Rows: ".$this->expected_num_rows."\n";
            $i=1;

            ImportedVoterMaster::where($code, '<', 1)->chunkById(500, function($null_districts) use (&$i, $code) {
                
                
                $found_match_house = 0;
                $found_match_street = 0;
                $have = 0;
                $no_conflict = 0;
                $conflict = 0;
                $towns = [];
                foreach ($null_districts as $nd) {
                    echo $this->progress($i++)."\r";

                    $other_household = ImportedVoterMaster::where('household_id', $nd->household_id)
                                                          ->where('id', '!=', $nd->id)
                                                          ->where($code, '>', 0)
                                                          ->first();

                    $check = ['congress_district', 'city_code', 'county_code', 'senate_district', 'house_district', 'governor_district', 'ward', 'precinct'];

                    
                    if ($other_household) {
                        foreach ($check as $field) {
                            if (!$nd->$field && $other_household->$field)
                                $nd->$field  = $other_household->$field;
                        }
                        //dd($nd);
                        $nd->save();
                        $found_match_house++;
                    } else {
                        if ($nd->address_city && $nd->address_street) {
                            $have++;
                            $ten_others = ImportedVoterMaster::where('address_city', $nd->address_city)
                                                             ->where('address_street', $nd->address_street)
                                                             ->where($code, '>', 0)
                                                             ->take(50)
                                                             ->inRandomOrder()
                                                             ->get();
                            
                            

                            $fields = [];
                            foreach ($ten_others as $other) {
                                foreach ($check as $field) {
                                    if (!$other->$field) {
                                        continue;
                                    }
                                    if (isset($fields[$field][$other->$field])) {
                                        $fields[$field][$other->$field] += 1;
                                    } else {
                                        $fields[$field][$other->$field] = 1;
                                    }
                                }
                                
                            }
                            //dd($fields);
                            foreach ($fields as $field_name => $field_arr) {
                                if (count($field_arr) == 1) {
                                    $nd->$field_name = array_key_first($field_arr);
                                } else {
                                    if (isset($towns[$nd->address_city])) {
                                        $towns[$nd->address_city][$field_name][] = $field_arr;
                                    } else {
                                        $towns[$nd->address_city][$field_name] = $field_arr;
                                    }
                                    $conflict++;
                                }
                            }
                            if ($nd->$code) {
                                $nd->save();
                                $found_match_street++;
                            }
                        }
                    }

                }
                echo "\nFound Match: House $found_match_house, Street $found_match_street.\n";
            });
        }
        //dd($towns);
        
        
    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
}
