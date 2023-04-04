<?php

namespace App\Console\Commands\Admin\UploadToMaster;

set_time_limit(-1);

use App\Import;
use App\VoterImport;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ImportMunicipality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:import_municipality {--import_id=}';

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
    public $district_lookup;

    public function __construct()
    {
        $this->district_lookup = [];
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $import_id = $this->option('import_id');
        if (! $import_id) {
            echo "Needs import_id like cf:import_municipality --import_id=\n";

            return;
        }
        $import = Import::find($import_id);
        if (! $import) {
            echo "Needs valid import_id not $import_id\n";

            return;
        }
        // add table
        $tablename = 'x_'.strtolower($import->municipality->name).'_'.$import->created_at->format('Y-m-d-H-i-s');
        $tablename = str_replace('-', '_', $tablename); // This was causing SQL errors
        $tablename = str_replace(' ', '_', $tablename); // This was causing SQL errors
        if (! Schema::connection('imports')->hasTable($tablename)) {
            $create = DB::connection('voters')->select('SHOW CREATE TABLE x_voters_MA_master');
            $create_sql = $create[0]->{'Create Table'};

            echo "Original Create: ".$create_sql."\n\n";

            $create_sql = str_replace('x_voters_MA_master', $tablename, $create_sql);
            
            // Remove regular keys (will get added back)
            $create_sql = preg_replace('/^\s+KEY `.*\n/', '', $create_sql);

            // Remove SPATIAL KEY
            // SPATIAL KEY `idx_location` (`location`),
            // SPATIAL KEY `x_voters_ma_master_location_spatial` (`location`)
            $create_sql = preg_replace('/\s+SPATIAL KEY `.*\n/', '', $create_sql);
            
            $create_sql = str_replace('),)', '))', $create_sql);

            echo "After Create: ".$create_sql."\n\n";

            DB::connection('imports')->statement($create_sql);
        }
        session(['import_table' => $tablename]);
        $import->table_name = $tablename;
        $import->started_at = Carbon::now();
        $import->save();
        //
        //dd($import->file_count);
        $records = $this->getMatchedRecords($import, $import->file_count);
        $import_count = 0;
        foreach ($records as $record) {

            foreach ($record as $fieldkey => $fieldval) {
                $record[$fieldkey] = preg_replace('/[\x{10000}-\x{10FFFF}]/u', "\xEF\xBF\xBD", $fieldval);
            }
            //dd($record);
            if (! isset($record['01_voter_id'])) {
                continue;
            }
            $voter_id = 'MA_'.$record['01_voter_id'];
            $vi = new VoterImport;
            $vi->setTable($tablename);
            //dd($vi);
            if ($vi::find($voter_id)) {
                $vi = VoterImport::find($voter_id);
                //dd($vi);
            }

            foreach ($record as $ind => $val) {
                if (stripos('a'.$val, 'N/A') > 0) {
                    $record[$ind] = "";
                } else {
                    $record[$ind] = trim($val);
                }
            }

            $vi->import_order = $import_count + 1;
            $vi->id = $voter_id;
            if (isset($record['02_last_name'])) {
                $vi->last_name = $record['02_last_name'];
            }
            if (isset($record['03_first_name'])) {
                $vi->first_name = $record['03_first_name'];
            }
            if (isset($record['04_middle_name'])) {
                $vi->middle_name = $record['04_middle_name'] ? $record['04_middle_name'] : null;
            }
            if (isset($record['05_suffix_name'])) {
                $vi->suffix_name = $record['05_suffix_name'] ? $record['05_suffix_name'] : null;
            }
            if (isset($record['06_address_number'])) {
                $vi->address_number = $record['06_address_number'];
            }
            if (isset($record['07_address_street'])) {
                $vi->address_street = $record['07_address_street'];
            }
            if (isset($record['08_address_post'])) {
                $vi->address_post = $record['08_address_post'] ? $record['08_address_post'] : null;
            }
            if (isset($record['09_address_apt'])) {
                $vi->address_apt = $record['09_address_apt'] ? $record['09_address_apt'] : null;
            }
            if (isset($record['10_address_zip'])) {
                $zip = $record['10_address_zip'];
                if (strlen($zip) > 5) {
                    $zip = str_pad($zip, 9, '0', STR_PAD_LEFT);
                } else {
                    $zip = str_pad($zip, 5, '0', STR_PAD_LEFT);
                }
                $vi->address_zip = substr($zip, 0, 5);
                if (strlen($zip) == 9) {
                    $vi->address_zip4 = substr($zip, 5, 4);
                }
            }
            if ($import->municipality) {
                $vi->address_city = $import->municipality->name;
            }
            $vi->address_state = 'MA';

            $vi->city_code = $import->municipality_id;

            if (isset($record['11_mailing_address'])) {
                $mailing_info = [];
                $mailing_info['address'] = $record['11_mailing_address'];
                $mailing_info['address2'] = $record['12_mailing_apt'];
                $mailing_info['city'] = $record['13_mailing_city'];
                if (isset($record['14_mailing_state'])) {
                    $mailing_info['state'] = $record['14_mailing_state'];
                } else {
                    $mailing_info['state'] = 'MA';
                }
                
                $mzip = $record['15_mailing_zip'];
                if (strlen($mzip) > 5) {
                    $mzip = str_pad($mzip, 9, '0', STR_PAD_LEFT);
                } else {
                    $mzip = str_pad($mzip, 5, '0', STR_PAD_LEFT);
                }
                $mzip1 = substr($mzip, 0, 5);
                if (strlen($mzip) == 9) {
                    $mzip2 = substr($mzip, 5, 4);
                    $mzip = $mzip1.'-'.$mzip2;
                }
                $mailing_info['zip'] = $mzip;
                $vi->mailing_info = $mailing_info;
            }

            if (isset($record['16_party'])) {
                $vi->party = $record['16_party'];
            }
            if (isset($record['17_dob'])) {
                $dob = Carbon::parse($record['17_dob']);
                if ($dob) {
                    $vi->dob = $dob;
                }
            }
            if (isset($record['18_registration_date'])) {
                $registration_date = Carbon::parse($record['18_registration_date']);
                if ($registration_date) {
                    $vi->registration_date = $registration_date;
                }
            }
            if (isset($record['19_ward_code'])) {
                $vi->ward = $record['19_ward_code'];
            }
            if (isset($record['20_precinct_code'])) {
                $precinct = $record['20_precinct_code'];
                if (is_numeric($precinct)) {
                    $precinct = (int) $precinct;
                }
                $vi->precinct = $precinct;
            }
            if (isset($record['21_congressional_district'])) {
                $vi->congress_district = $record['21_congressional_district'];
            }
            if (isset($record['22_senate_district'])) {
                $vi->senate_district = $record['22_senate_district'];
            }
            if (isset($record['23_house_district'])) {
                $vi->house_district = $record['23_house_district'];
            }
            if (isset($record['24_voter_status'])) {
                $vi->voter_status = $record['24_voter_status'];
            }
            if (isset($record['25_gender'])) {
                $vi->gender = $record['25_gender'];
            }

            $vi->origin_method = 'MUNICIPAL_FILE';

            $vi->location = DB::raw("POINT(0,0)");
            $vi->save();

            foreach (['governor_district',
                     'congress_district',
                     'senate_district',
                     'house_district', ] as $district_type) {
                if (! $vi->$district_type) {
                    $district_num = $this->calculateDistrictCode($vi, $district_type);
                    if ($district_num > 0) {
                        $vi->$district_type = $district_num;
                    }
                }
            }
            $vi->save();

            $import_count++;
            $import->imported_count = $import_count;
            $import->save();
        }
        $import->imported_at = Carbon::now();
        $import->save();
        //dd($tablename);
        //dd($records);
    }

    public function getMatchedRecords($import, $limit)
    {
        $filepath = $import->file;
        if (! $limit) {
            $limit = 10000;
        }

        $dfile = new \SplFileObject(storage_path().'/app/'.$filepath, 'r');
        $delimiter = $this->detectDelimiter($dfile);

        $file = new \SplFileObject(storage_path().'/app/'.$filepath, 'r');
        $firstrow = $file->fgetcsv($delimiter);
        //dd($firstrow, $this->field_map);
        $lookup = [];
        foreach ($import->column_map as $index => $val) {
            if ($val) {
                $lookup[$val] = $index;
            }
        }
        $indexmap = [];
        foreach ($firstrow as $index => $longfield) {
            if (isset($lookup[trim($longfield)])) {
                $indexmap[$index] = $lookup[trim($longfield)];
            }
        }
        //dd($indexmap);
        $rows = [];
        for ($i = 0; $i < $limit; $i++) {
            $rawrow = $file->fgetcsv($delimiter);
            $row = [];

            if (!$rawrow) {
                continue;
            }
            foreach ($rawrow as $index => $val) {
                if (isset($indexmap[$index])) {
                    $row[$indexmap[$index]] = $val;
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function detectDelimiter($file)
    {
        $delimiter = ',';
        $possibilities = ["\t", ';', '|', ','];
        $data_1 = [];
        $data_2 = [];

        foreach ($possibilities as $d) {
            $data_1 = $file->fgetcsv($d);
            if (count($data_1) > count($data_2)) {
                $delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
                $data_2 = $data_1;
            }
            $file->rewind();
        }

        return $delimiter;
    }

    public function calculateDistrictCode($vi, $district_type)
    {
        //dd($vi,$vi->calculateDistrictCode($district_type));
        if (! isset($this->district_lookup[$district_type][$vi->city_code][$vi->ward][$vi->precinct])) {
            $this->district_lookup[$district_type][$vi->city_code][$vi->ward][$vi->precinct] = (int) $vi->calculateDistrictCode($district_type) + 0;
        //echo "$district_type getting district\n";
        } else {
            //echo "$district_type using cache\n";
        }
        //dd($this->district_lookup[$district_type][$vi->city_code][$vi->ward][$vi->precinct]);
        //dd($this->district_lookup);
        return $this->district_lookup[$district_type][$vi->city_code][$vi->ward][$vi->precinct];
    }
}
