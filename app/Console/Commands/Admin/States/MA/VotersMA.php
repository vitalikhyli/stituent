<?php

namespace App\Console\Commands\Admin\States\MA;

use App\Console\Commands\Admin\States\NationalMaster;

use App\District;
use App\Municipality;
use App\County;
use App\Models\ImportedMADistrict;
use App\Models\ImportedMADistrictVoter;
Use App\Models\ImportedVoterMaster;

use Carbon\Carbon;

use DB;
use Schema;
use Illuminate\Database\Schema\Blueprint;


class VotersMA extends NationalMaster
{
    protected $signature                = 'cf:ma_voters {--analyze}

                                                        {--clear_districts} 
                                                        {--clear_district_voter}

                                                        {--file_path_voters=}
                                                        {--file_path_districts=}
                                                        {--file_path_district_voter=}

                                                        {--checkDistricts}
                                                        {--checkMunicipalities}';
    protected $description              = '';
    public $state                       = 'MA';
    public $districts_count             = ['F' => 9, 'S' => 40, 'H' => 160];
    public $municipal_count             = ['cities' => 351, 'counties' => 14];

    public $districts_table             = 'i_ma_districts_import';
    public $pivot_table                 = 'i_ma_district_voter_import';

    public $lookup_districts            = [];


    ////////////////////////////////////////////////////////////////////////////////
    //
    // REQUIRED FUNCTIONS:
    //

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //----------------------------------------------------------------------------
        // OPTIONS

        // dd(
        //     $this->option('clear_districts'),
        //     $this->option('clear_district_voter'),
        //     $this->option('file_path_districts'),
        //     $this->option('file_path_district_voter'),
        //     $this->option('run_checkDistricts'),
        //     $this->option('run_checkMunicipalities'),
        // );
        
        $file_path_voters         = ($this->option('file_path_voters')) ?: null;
        $file_path_districts      = ($this->option('file_path_districts')) ?: null;
        $file_path_district_voter = ($this->option('file_path_district_voter')) ?: null;

        // if ($this->option('checkDistricts') != 'done')      $this->checkDistricts();
        // if ($this->option('checkMunicipalities') != 'done') $this->checkMunicipalities();

        //----------------------------------------------------------------------------
        //

        echo "\n";
        // if (config('app.env') != 'local') dd('Cannot run in live yet.');
        
        if ($this->option('analyze'))              $this->figureOutFields();
        if ($this->option('clear_districts'))      $this->truncateIfExists($this->districts_table);
        if ($this->option('clear_district_voter')) $this->truncateIfExists($this->pivot_table);

        $has_districts      = $this->checkHasTableAndNotEmpty($this->districts_table);
        $has_district_voter = $this->checkHasTableAndNotEmpty($this->pivot_table);

        if (!$file_path_districts && !$has_districts) {
            $this->info('Districts table needed');
            $file_path_districts = $this->selectFilePath($this->storage_subdir, 
                                               $what_for = 'DISTRICTS');
        }

        if (!$file_path_district_voter && !$has_district_voter) {
            $this->info('District-voter pivot table needed');
            $file_path_district_voter = $this->selectFilePath($this->storage_subdir, 
                                               $what_for = 'DISTRICT-AND-VOTERS PIVOT TABLE');
        }

        if (!$has_districts)        $this->uploadDistrictsFile($file_path_districts);
        if (!$has_district_voter)   $this->uploadPivotFile($file_path_district_voter);

        if (!$file_path_voters) {
            $file_path_voters = $this->selectFilePath($this->storage_subdir, "the VOTER import");
        }
        $this->importNewMaster($file_path_voters);
    }

    public function forEachRow($switch, $row, $row_num)
    {
        switch ($switch) {
            case 'districts':
                return $this->importDistrictRow($row, $row_num);
                break;

            case 'district_voter':
                return $this->importDistrictVoterRow($row, $row_num);
                break;

            case 'voters':
                return $this->importVoterRow($row, $this->firstrow, $row_num);
                break;
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // MASS. FUNCTIONS:
    //

    public function uploadPivotFile($file_path)
    {
        echo "\n";
        $this->info('Uploading District-Voter Pivot...');

        Schema::connection('voters')->dropIfExists($this->pivot_table);
        Schema::connection('voters')->create($this->pivot_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cf_voter_id')->nullable()->index();
            $table->string('cf_type')->nullable();              // For ease of lookup
            $table->string('cf_code')->nullable();              // For ease of lookup
            $table->unsignedInteger('rec_id')->nullable();
            $table->string('voter_id')->nullable()->index();
            $table->string('county_voter_id')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->timestamps();
        });

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter    = $this->detectDelimiter($file_path);
        $this->firstrow     = $this->getFirstRow($file_path);
        $this->start_time   = Carbon::now();

        $log                = $this->createErrorLog($name = $this->pivot_table);

        $this->massInsert($file_path,
                          $switch = 'district_voter',
                          $log,
                          1000);
    }


    public function importDistrictVoterRow($row, $rownum)
    {
        $csv = $this->englishColumnNames($row, $this->firstrow);

        // "RECID" => "0"
        // "StateVoterid" => "09CWN2287000"
        // "CountyVoterID" => ""
        // "DistrictID" => "669510"

        $pivot = new ImportedMADistrictVoter;

        $pivot->cf_voter_id     = $this->state.'_'.$csv['StateVoterid'];
        $pivot->rec_id          = $csv['RECID'];
        $pivot->voter_id        = $csv['StateVoterid'];
        $pivot->county_voter_id = $csv['CountyVoterID'];
        $pivot->district_id     = $csv['DistrictID'];

        if (isset($this->lookup_districts[$pivot->district_id])) {
            $district = $this->lookup_districts[$pivot->district_id];
            $pivot->cf_type         = $district->cf_type;
            $pivot->cf_code         = $district->cf_code;
        } else {
            if ($district = ImportedMADistrict::where('district_id', $pivot->district_id)
                                              ->first()) {
                $this->lookup_districts[$pivot->district_id] = $district;
                $pivot->cf_type         = $district->cf_type;
                $pivot->cf_code         = $district->cf_code;
            }
        }

        //$pivot->save(); 
        return $pivot;  
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function uploadDistrictsFile($file_path)
    {
        echo "\n";
        $this->info('Uploading District...');

        Schema::connection('voters')->dropIfExists($this->districts_table);
        Schema::connection('voters')->create($this->districts_table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cf_type')->nullable();
            $table->string('cf_code')->nullable();
            $table->unsignedInteger('district_id')->nullable();
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('type')->nullable();
            $table->string('county_type')->nullable();
            $table->string('county')->nullable();
            $table->unsignedInteger('fips')->nullable(); // Federal Info Processing Standards
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        $this->expected_num_rows  = $this->expectedNumRows($file_path);
        $this->delimiter    = $this->detectDelimiter($file_path);
        $this->firstrow     = $this->getFirstRow($file_path);
        $this->start_time   = Carbon::now();

        $log                = $this->createErrorLog($name = $this->districts_table);

        $this->openHandleAndGoThrough($file_path,
                                      $switch = 'districts',
                                      $log);

    }

    public function importDistrictRow($row, $rownum)
    {
        $csv = $this->englishColumnNames($row, $this->firstrow);

        // "DistrictID" => "669369"
        // "DistrictName" => "NORTON TOWN"
        // "DistrictCode" => "NORTON"
        // "DistrictType" => "Town District"
        // "CountyDistrictType" => "CityTown_Name"
        // "CountyName" => "Bristol"
        // "FIPS" => "005"
        // "OrigDistrictName" => "NORTON"

        $district = new ImportedMADistrict;

        $district->district_id      = $csv['DistrictID'];
        $district->name             = $csv['DistrictName'];
        $district->code             = $csv['DistrictCode'];
        $district->type             = $csv['DistrictType'];
        $district->county_type      = $csv['CountyDistrictType'];
        $district->county           = $csv['CountyName'];
        $district->fips             = $csv['FIPS'];
        $district->original_name    = $csv['OrigDistrictName'];

        if ($cf = $this->lookupDistrict($district)) {
            $district->cf_type          = $cf['type'];
            $district->cf_code          = $cf['code'];
        }

        $district->save();
        
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function importVoterRow($row, $firstrow, $row_num)
    {
        //------------------------------------------------

        // array:56 [
        //   0 => "9743"
        //   1 => "12ZZY0787000"
        //   2 => null
        //   3 => "025"
        //   4 => null
        //   5 => null
        //   6 => "ZHANG"
        //   7 => "ZACHARY"
        //   8 => "SHIN"
        //   9 => null
        //   10 => null
        //   11 => "MR"
        //   12 => "SR"
        //   13 => "07/03/2017"
        //   14 => "00/00/1987"
        //   15 => null
        //   16 => "Democratic"
        //   17 => null
        //   18 => "M"
        //   19 => null
        //   20 => null
        //   21 => null
        //   22 => "2"
        //   23 => null
        //   24 => null
        //   25 => "1575 TREMONT ST APT 602"
        //   26 => "BSMT"
        //   27 => "1575"
        //   28 => "TREMONT"
        //   29 => "ST"
        //   30 => "S"
        //   31 => "NE"
        //   32 => "BOSTON"
        //   33 => "MA"
        //   34 => "02120"
        //   35 => "1629"
        //   36 => "APT"
        //   37 => "602"
        //   38 => null
        //   39 => "1575 TREMONT ST APT 602"
        //   40 => "BSMT"
        //   41 => "1575"
        //   42 => "TREMONT"
        //   43 => "ST"
        //   44 => "S"
        //   45 => "E"
        //   46 => "BOSTON"
        //   47 => "MA"
        //   48 => "02120"
        //   49 => "1629"
        //   50 => "APT"
        //   51 => "602"
        //   52 => null
        //   53 => null
        //   54 => null
        //   55 => null
        // ]

        //-----------------------------------------------------------------------

        $map = [
                0   => 'import_order',
                1   => 'voter_id',
                //2 => '',
                3   => 'county_code',
                //4 => '',
                //5 => '',
                6   => 'last_name',
                7   => 'first_name',
                8   => 'middle_name',
                //9 => '',
                //10 => '',
                11 =>  'name_title',
                12 =>  'suffix_name',
                13  => 'registration_date',
                14  => 'yob',
                //15 => '',
                16  => 'party',
                //17 => '',
                18  => 'gender',
                //19 => '',
                //20 => '',
                //21 => '',
                22 => 'voter_status',
                //23 => '',
                //24 => '',
                //////25 => (full address) <-------- Assume Mailing b/c there was a Wisconsin
                //26 => '',
                27  => 'mailing_address_number',
                28  => 'mailing_address_street',
                29  => 'mailing_address_street_type',
                //30 => '',
                //31 => '',
                32  => 'mailing_address_city',
                33  => 'mailing_address_state',
                34  => 'mailing_address_zip',
                35  => 'mailing_address_zip4',
                36  => 'mailing_address_apt_type',
                37  => 'mailing_address_apt',
                //38 => '',
                //////39 => (full address) <-------- Assume Residential
                40 =>  '',
                41  => 'address_number',
                42  => 'address_street',
                43  => 'address_street_type',
                //44 => '',
                //45 => '',
                46  => 'address_city',
                47  => 'address_state',
                48  => 'address_zip',
                49  => 'address_zip4',
                50  => 'address_apt_type',
                51  => 'address_apt',
                //52 => '',
                //53 => '',
                //54 => '',
                //55 => '',
               ];

        //-----------------------------------------------------------------------

        // 0 => "import_order"
        // 1 => "id"
        // 2 => "full_name"
        // 3 => "full_name_middle"
        // 4 => "household_id"
        // 5 => "mass_gis_id"
        // 6 => "full_address"
        // 7 => "elections"
        // 8 => "name_title"
        // 9 => "first_name"
        // 10 => "middle_name"
        // 11 => "last_name"
        // 12 => "suffix_name"
        // 13 => "address_prefix"
        // 14 => "address_number"
        // 15 => "address_fraction"
        // 16 => "address_street"
        // 17 => "address_street_type"
        // 18 => "address_post"
        // 19 => "address_apt_type"
        // 20 => "address_apt"
        // 21 => "address_city"
        // 22 => "address_state"
        // 23 => "address_zip"
        // 24 => "address_zip4"
        // 25 => "address_lat"
        // 26 => "address_long"
        // 27 => "gender"
        // 28 => "party"
        // 29 => "dob"
        // 30 => "yob"
        // 31 => "registration_date"
        // 32 => "voter_status"
        // 33 => "ethnicity"
        // 34 => "head_household"
        // 35 => "state"
        // 36 => "governor_district"
        // 37 => "congress_district"
        // 38 => "senate_district"
        // 39 => "house_district"
        // 40 => "county_code"
        // 41 => "city_code"
        // 42 => "ward"
        // 43 => "precinct"
        // 44 => "spouse_name"
        // 45 => "cell_phone"
        // 46 => "home_phone"
        // 47 => "deceased"
        // 48 => "deceased_date"
        // 49 => "mailing_info"
        // 50 => "emails"
        // 51 => "business_info"
        // 52 => "alternate_districts"
        // 53 => "archived_at"
        // 54 => "origin_method"
        // 55 => "original_import"
        // 56 => "created_by"
        // 57 => "updated_by"
        // 58 => "deleted_at"
        // 59 => "created_at"
        // 60 => "updated_at"
        
        //-----------------------------------------------------------------------

        $csv = $this->mapColumnNames($row, $map);

        $import = new ImportedVoterMaster;

        $import->import_order       = $csv['import_order'];
        $import->state              = $this->state;
        $import->original_import    = [$this->original_import_date
                                       .'_'.$this->origin_method => $csv];
        $import->origin_method      = $this->origin_method;

        $their_districts = $this->getCFDistrictArray($csv['voter_id']);
        
        $import->congress_district  = $their_districts['F'];
        $import->house_district     = $their_districts['H'];
        $import->senate_district    = $their_districts['S'];
        $import->county_code        = $their_districts['county'];
        $import->city_code          = $their_districts['city'];
        $import->ward               = $their_districts['ward'];
        $import->precinct           = $their_districts['precinct'];

        $import->id                 = $this->state.'_'.$csv['voter_id'];

        $import->first_name         = $csv['first_name'];
        $import->last_name          = $csv['last_name'];
        $import->middle_name        = $csv['middle_name'];
        $import->suffix_name        = $csv['suffix_name'];

        $voter_status = 'I';
        if ((int)$csv['voter_status'] > 0) {
            $voter_status = 'A';
        }
        $import->voter_status       = $voter_status;

        $import->registration_date  = Carbon::parse($csv['registration_date'])->toDateString();
        $import->yob                = Carbon::parse($csv['yob'])->format('Y') + 1;

        $import->gender             = (!$csv['gender']) ? null : $csv['gender'];
        $import->party              = $this->formatParty($csv['party']);

        $import->address_number         = $csv['address_number'];
        $import->address_street         = trim($csv['address_street'].' '
                                           .$csv['address_street_type']);
        $import->address_street_type    = $csv['address_street_type'];
        $import->address_city           = $csv['address_city'];
        $import->address_apt            = trim($csv['mailing_address_apt_type']
                                                    .' '.$csv['mailing_address_apt']);
        $import->address_apt            = ($import->address_apt == '') ? null : $import->address_apt;
        $import->address_state          = $csv['address_state'];
        $import->address_zip            = $this->correctZip($csv['address_zip']);
        $import->address_zip4           = $csv['address_zip4'];

        $import->mailing_info       = ['address' => trim($csv['mailing_address_number']
                                                    .' '.$csv['mailing_address_street']
                                                    .' '.$csv['mailing_address_street_type']),
                                       'address2' => trim($csv['mailing_address_apt_type']
                                                    .' '.$csv['mailing_address_apt']),
                                       'city' => trim($csv['mailing_address_city']),
                                       'state' => trim($csv['mailing_address_state']),
                                       'zip' => trim($this->correctZip($csv['mailing_address_zip'])),
                                       'zip4' => trim($csv['mailing_address_zip4'])
                                      ];

        //$import->save();
                                      //dd($import);
        return $import;

    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function lookupDistrict($import)
    {
        $lookup = ['US Congressional District'  => 'congress',
                   'State Senate District'      => 'senate',
                   'State House District'       => 'house',
                   'County'                     => 'county',
                   'Precinct'                   => 'precinct',
                   'City Ward'                  => 'ward',
                   'City'                       => 'city',  // No distinction in CF
                   'Town District'              => 'city'   // No distinction in CF
                  ];

        $short_code = $lookup[$import->type];

        if ($short_code == 'congress') {
            $english = $this->englishOrdinal($import->name); // i.e. "6" --> "Sixth"
            $cf = District::where('state', $this->state)
                          ->where('type', 'F')
                          ->where('name', $english)
                          ->first();
            if ($cf) return ['type' => 'F', 'code' => $cf->code];
        }

        if ($short_code == 'senate') {

            // BERKSHIRE-HAMPSHIRE-FRANKLIN-HAMPDEN
            // BRISTOL-NORFOLK
            // BRISTOL-PLYMOUTH 1
            // BRISTOL-PLYMOUTH 2
            // CAPE-ISLANDS
            // ESSEX 1
            // ESSEX 2
            // ESSEX 3
            // ESSEX-MIDDLESEX 1
            // ESSEX-MIDDLESEX 2
            // HAMPDEN

            $alpha = $this->alphaOnly($import->name);
            if (strpos($alpha, ' ') === false) {
                $english = $alpha;
            } else {
                $words = explode(' ', $alpha);
                if (count($words) == 2) {
                    $english = $words[0].' & '.$words[1];
                } elseif (count($words) > 2) {
                    $last = array_pop($words);
                    $english = implode(', ', $words).' & '.$last;
                }
            }
            $english = trim($this->englishOrdinal($import->name).' '.$english);
            $cf = District::where('state', $this->state)
                          ->where('type', 'S')
                          ->where('name', $english)
                          ->first();

            if ($cf) return ['type' => 'S', 'code' => $cf->code];
        }

        if ($short_code == 'house') {

            // BERKSHIRE 4
            // BRISTOL 01
            // BARNSTABLE 4
            // BARNSTABLE-DUKES-NANTUCKET

            $alpha = $this->alphaOnly($import->name);
            if (strpos($alpha, ' ') === false) {
                $english = $alpha;
            } else {
                $words = explode(' ', $alpha);
                if (count($words) == 2) {
                    $english = $words[0].' & '.$words[1];
                } elseif (count($words) > 2) {
                    $last = array_pop($words);
                    $english = implode(', ', $words).' & '.$last;
                }
            }
            $english = trim($this->englishOrdinal($import->name).' '.$english);
            $cf = District::where('state', $this->state)
                          ->where('type', 'H')
                          ->where('name', $english)
                          ->first();

            if ($cf) return ['type' => 'H', 'code' => $cf->code];
        }

        if ($short_code == 'city') {

            $cf = Municipality::where('state', $this->state)
                              ->where('name', $import->code) // Name is this for some reason
                              ->first();

            if ($cf) return ['type' => 'city', 'code' => $cf->code];    

        }

        if ($short_code == 'county') {

            $cf = County::where('state', $this->state)
                        ->where('name', $import->code) // Name is this for some reason
                        ->first();

            if ($cf) return ['type' => 'county', 'code' => $cf->code];  

        }

        if ($short_code == 'ward') {

            // ATTLEBORO CITY WARD 6
            // BEVERLY CITY WARD 1
            // SPRINGFIELD CITY WARD 1
            // BOSTON CITY WARD 04

            $num = filter_var($import->name, FILTER_SANITIZE_NUMBER_INT);
            if (is_numeric($num)) return ['type' => 'ward', 'code' => $num * 1];
            return null;

        }

        if ($short_code == 'precinct') {

            // BOSTON 22-13
            // BOURNE 1
            // BOXBOROUGH
            // ATTLEBORO 6-B

            $words = explode(' ', $import->name);
            $last = $words[count($words) - 1];
            if (strpos($last, '-') !== false) {   // Is like 6-B
                $parts = explode('-', $last);
                $precinct = $parts[1];
            } else {
                $precinct = $last;
            }
            if (is_numeric($precinct)) {
                $precinct *= 1;                   // Remove leading zeros
            } elseif (strlen($precinct) > 2) {
                $precinct = null;                 // Avoid just the town (As long as "LEE")
            }
            return ['type' => 'precinct', 'code' => $precinct];
        }
        
    }

    public function getCFDistrictArray($voter_id)
    {
        $cf = ImportedMADistrictVoter::where('voter_id', $voter_id)->get();

        $arr  = ['F'          => null,
                 'S'          => null,
                 'H'          => null,
                 'county'     => null,
                 'ward'       => null,
                 'city'       => null,
                 'ward'       => null,
                 'precinct'   => null];

        foreach($cf as $dist) {
            $arr[$dist->cf_type] = $dist->cf_code;
        }

        return $arr;
    }

    ////////////////////////////////////////////////////////////////////////////////////////

    public function figureOutFields()
    {
        $test_table = $this->selectPreviousMaster();
        session(['table_while_importing_master' => $test_table]);
        $all = [];

        $i=0;
        
        ImportedVoterMaster::chunk(10000, function ($voters) use (&$all, &$i) {
            foreach ($voters as $voter) {
                $import = $this->rekeyStartingAtZero($voter->original_import);
                echo  $i++."\t".count($import)."\r";
                foreach($import as $key => $column) {
                    if (!isset($all[$key])) {
                        $all[$key] = null;
                        
                    }
                    if ($column) {
                        $all[$key] = $column;
                    }
                }
            }
        });
        ksort($all);
        $num_nulls = collect($all)->filter(function ($item) {
                                    return $item === null;
                                })->count();
        $num_total = collect($all)->count();
        dd($all, $num_nulls.' nulls - '.number_format(100 * $num_nulls/$num_total).'% are null, '.$this->condescendingNickname()); 
    }

    ////////////////////////////////////////////////////////////////////////////////////
    //
    // PRE-PROCESSING CHECKS
    //

    // public function checkDistricts()
    // {
    //     $districts_ok = false;

    //     while ($districts_ok == false) {

    //         $districts_problems = [];

    //         foreach(['F' => 'Federal', 'S' => 'Senate', 'H' => 'House'] as $code => $name)  {
    //             $count = District::where('state', $this->state)->where('type', $code)->count();
    //             if ($count < $this->districts_count[$code]) {
    //                 $districts_problems[] = $name.' ('.($this->districts_count[$code] - $count).' of '
    //                 .$this->districts_count[$code].' missing)';
    //             }
    //         }

    //         if ($districts_problems) {

    //             $ok = $this->confirm('CONFIRM: Missing districts for: '
    //                                  .implode(', ', $districts_problems)
    //                                  .'...proceed anyway?');

    //             if (!$ok) {

    //                 $this->commandMustExistOrDie($this->stateSubCommand('districts'));

    //                 $run = $this->confirm('Run '.$this->stateSubCommand('districts').'?', true);

    //                 if (!$run) {
    //                     dd('Process stopped.');
    //                 } else {
    //                     $this->call('cf:'.strtolower($this->state).'_districts');
    //                 }
    //             }

    //         } else {

    //             $this->info('* PRE-IMPORT CHECK: All '.$this->state.' Districts accounted for.');
    //             $districts_ok = true;

    //         }

    //     }

    // }

    // public function checkMunicipalities()
    // {
    //     $local_ok = false;

    //     while ($local_ok == false) {

    //         $local_problems = [];

    //         $count = County::where('state', $this->state)->count();
    //         if ($count < $this->municipal_count['counties']) {
    //             $local_problems[] = 'Counties ('.($this->municipal_count['counties'] - $count).' of '
    //             .$this->municipal_count['counties'].' missing)';
    //         }

    //         $count = Municipality::where('state', $this->state)->count();
    //         if ($count < $this->municipal_count['cities']) {
    //             $local_problems[] = 'Cities ('.($this->municipal_count['cities'] - $count).' of '
    //             .$this->municipal_count['cities'].' missing)';
    //         }

    //         if ($local_problems) {

    //             $ok = $this->confirm('CONFIRM: Missing local jurisdictions for: '
    //                                  .implode(', ', $local_problems)
    //                                  .'...proceed anyway?');

    //             if (!$ok) {

    //                 $this->commandMustExistOrDie($this->stateSubCommand('cities'));

    //                 $run = $this->confirm('Run '.$this->stateSubCommand('cities').'?', true);

    //                 if (!$run) {
    //                     dd('Process stopped.');
    //                 } else {
    //                     $this->call('cf:'.strtolower($this->state).'_cities');
    //                 }
    //             }

    //         } else {

    //             $this->info('* PRE-IMPORT CHECK: All '.$this->state.' local jurisdictions accounted for.');
    //             $local_ok = true;

    //         }

    //     }
    // }    
}
