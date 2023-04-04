<?php

namespace App\Console\Commands;

use App\MassGIS;
use App\Person;
use App\VoterMaster;
use Illuminate\Console\Command;

use App\Traits\Admin\FileProcessingTrait;
use App\Models\ImportedVoterMaster;
use Carbon\Carbon;
use App\Console\Commands\Admin\States\NationalMaster;
use Str;

class PullFromMassGIS extends NationalMaster
{
    public $state = 'MA';
    use FileProcessingTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:massgis {--chunk=} {--import} {--city=}';

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

        $model = VoterMaster::class;
        if ($this->option('import')) {
            $model = ImportedVoterMaster::class;
            $table = $this->selectPreviousMaster();
            session(['table_while_importing_master' => $table]);
        }
        $street_abbreviations = [
            ' ST ',
            ' DR ',
            ' LN ',
            ' AVE ',
            ' TRL ',
            ' RD ',
            ' HTS ',
            ' TPKE ',
            ' TER ',
            ' CIR ',
            ' VLG ',
            ' VIL ',
            ' EXT ',
            ' HWY ',
            ' CT ',
            ' PKWY ',
            ' BLVD ',
            ' PL ',
            ' WY ',
            ' HL ',
            ' MNR ',
            ' LNDG ',
            ' GRN ',
            ' PKY ',
            ' VLGE ',
            ' VLY ',
            ' APTS ',
            ' SQ ',
        ];
        $street_full = [
            ' STREET',
            ' DRIVE',
            ' LANE',
            ' AVENUE',
            ' TRAIL',
            ' ROAD',
            ' HEIGHTS',
            ' TURNPIKE',
            ' TERRACE',
            ' CIRCLE',
            ' VILLAGE',
            ' VILLAGE',
            ' EXTENSION',
            ' HIGHWAY',
            ' COURT',
            ' PARKWAY',
            ' BOULEVARD',
            ' PLACE',
            ' WAY',
            ' HILL',
            ' MANOR',
            ' LANDING',
            ' GREEN',
            ' PARKWAY',
            ' VILLAGE',
            ' VALLEY',
            ' APARTMENTS',
            ' SQUARE',
        ];

        //dd($this->compassDirections('E WILLY STREET', $street_full));
        $chunk = 1000;
        if ($this->option('chunk')) {
            $chunk = $this->option('chunk');
        }

        $query = $model::query();

        if ($this->option('city')) {
            $query->where('city_code', $this->option('city'));
            $this->expected_num_rows    = $model::where('city_code', $this->option('city'))
                                                ->where('mass_gis_id', '<', 1)
                                                ->withTrashed()
                                                ->count();

        } else {
            $this->expected_num_rows    = $model::where('mass_gis_id', '<', 1)
                                                ->withTrashed()
                                                ->count();
            
        }

        $this->start_time           = Carbon::now();
        $i=1;
        date('Y-m-d h:i:s')." Running.\n";

        
        
        $query->where('mass_gis_id', '<', 1)
              ->withTrashed()
              ->chunkById($chunk, function ($master_voters) use (&$i, $model, $street_abbreviations, $street_full) {
        
            $matched_gis_count = 0;
            $matched_household_count = 0;

            
            foreach ($master_voters as $voter) {
                //echo $this->progress($i++)."\r";
                if ($voter->address_number < 1) {
                    continue;
                }

                $address_street = strtoupper($voter->address_street);
                echo $address_street." => ";
                $address_street = str_replace($street_abbreviations, $street_full, $address_street.' ');
                $address_street = $this->compassDirections($address_street, $street_full);

                echo $address_street." => ";
                $massgis = MassGIS::where('geographic_town_id', $voter->city_code)
                                  ->where('street_name', $address_street)
                                  ->where('address_number', $voter->address_number)
                                  ->first();

                

                if (!$massgis) {
                    $other_household = VoterMaster::where('household_id', $voter->household_id)
                                                          ->where('id', '!=', $voter->id)
                                                          ->where('mass_gis_id', '>', 0)
                                                          ->first();
                    if ($other_household) {
                        $massgis = MassGIS::find($other_household->mass_gis_id);
                        $matched_household_count++;
                    }
                }

                if (!$massgis) {
                    // i.e. 10th => TENTH
                    $temp_address = $this->convertNumeric($address_street);
                    echo '['.$temp_address.']';
                    if ($temp_address != $address_street) {
                        $massgis = MassGIS::where('geographic_town_id', $voter->city_code)
                                          ->where('street_name', $temp_address)
                                          ->where('address_number', $voter->address_number)
                                          ->first();
                    }
                }
                echo "\n";
                if ($massgis) {
                    $matched_gis_count++;
                }
                // if(1 === preg_match('~[0-9]~', $address_street)){
                //     dd($voter, $address_street, $temp_address);
                // }

                if (!$massgis) {

                    if ($voter->mass_gis_id) {
                        $voter->mass_gis_id = 0; // 0 means tried but couldn't match
                        $voter->save();
                    }
                    //echo date('Y-m-d h:i:s').' No Match: '.$voter->address_number.' '.$voter->address_street.' '.$voter->city_code."\n";
                    //dd($voter);
                    continue;
                }

                //dd($massgis);

                $voter->address_lat = $massgis->address_lat;
                $voter->address_long = $massgis->address_long;
                $voter->mass_gis_id = $massgis->id;
                $voter->save();

                $affected = $model::where('city_code', $voter->city_code)
                                   ->where('address_street', $voter->address_street)
                                   ->where('address_number', $voter->address_number)
                                   ->update([
                                        'address_lat' => $massgis->address_lat,
                                        'address_long' => $massgis->address_long,
                                        'mass_gis_id' => $massgis->id,
                                   ]);
                echo $this->progress($i++)."\r";
                // Person::where('city_code', $voter->city_code)
                //       ->where('address_street', $voter->address_street)
                //       ->where('address_number', $voter->address_number)
                //       ->update([
                //             'address_lat' => $massgis->address_lat,
                //             'address_long' => $massgis->address_long,
                //             'mass_gis_id' => $massgis->id,
                //        ]);
            }
            echo date('Y-m-d h:i:s')." Matched GIS: $matched_gis_count, matched HH: $matched_household_count \n";
        });
    }

    public function forEachRow($switch, $row, $row_num)
    {
        // Required by abstract parent class
    }
    public function convertNumeric($street_name) 
    {
        if(1 === preg_match('~[0-9]~', $street_name)){
            $arr = [
                '1ST' => 'First',
                '2ND' => 'Second',
                '3RD' => 'Third',
                '4TH' => 'Fourth',
                '5TH' => 'Fifth',
                '6TH' => 'Sixth',
                '7TH' => 'Seventh',
                '8TH' => 'Eighth',
                '9TH' => 'Ninth',
                '10TH' => 'Tenth',
                '11TH' => 'Eleventh',
                '12TH' => 'Twelfth',
                '13TH' => 'Thirteenth',
                '14TH' => 'Fourteenth',
                '15TH' => 'Fifteenth',
                '16TH' => 'Sixteenth',
                '17TH' => 'Seventeenth',
                '18TH' => 'Eighteenth',
                '19TH' => 'Nineteenth',
                '20TH' => 'Twentieth',
            ];
            foreach ($arr as $num => $str) {
                $street_name = strtoupper(str_replace($num, $str, $street_name));
            }
        }
        return $street_name;
        
    }
    public function compassDirections($address_street, $street_full)
    {
        $new_address_street = $address_street;
        if (Str::startsWith($new_address_street, 'N ')) {
            $removed = trim($this->str_replace_first('N ', '', $new_address_street));
            if (!in_array(' '.$removed, $street_full)) {
                $new_address_street = "NORTH ".$removed;
            }
        }
        if (Str::startsWith($new_address_street, 'E ')) {
            $removed = trim($this->str_replace_first('E ', '', $new_address_street));
            if (!in_array(' '.$removed, $street_full)) {
                $new_address_street = "EAST ".$removed;
            }
        }
        if (Str::startsWith($new_address_street, 'S ')) {
            $removed = trim($this->str_replace_first('S ', '', $new_address_street));
            if (!in_array(' '.$removed, $street_full)) {
                $new_address_street = "SOUTH ".$removed;
            }
        }
        if (Str::startsWith($new_address_street, 'W ')) {
            $removed = trim($this->str_replace_first('W ', '', $new_address_street));
            if (!in_array(' '.$removed, $street_full)) {
                $new_address_street = "WEST ".$removed;
            }
        }
        if ($address_street != $new_address_street) {
            echo $address_street." => ".$new_address_street."\n";
        }
        return $new_address_street;
    }
    public function str_replace_first($needle, $replace, $haystack)
    {
        $newstring = $haystack;
        $pos = strpos($haystack, $needle);
        if ($pos !== false) {
            $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
        }
        return $newstring;
    }

}
