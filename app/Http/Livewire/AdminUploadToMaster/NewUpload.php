<?php

namespace App\Http\Livewire\AdminUploadToMaster;

use App\Import;
use App\VoterImport;
use App\VoterMaster;
use App\Team;

use Artisan;
use Carbon\Carbon;
use DB;

use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class NewUpload extends Component
{
    use WithPagination;

    public $step_1 = false;
    public $step_2 = false;
    public $step_3 = false;
    public $step_4 = false;
    public $step_5 = false;
    public $step_6 = false;

    public $import_id = null;
    
    public $state;
    public $available_states;

    public $municipality_id = null;
    public $municipality_lookup = null;
    public $municipalities = null;

    public $delimiter = ',';
    public $first_has_fields = true;

    public $firstrow;
    public $firstrecord;
    public $firstfive = [];

    public $field_map = [
        '01_voter_id' => null,
        '02_last_name' => null,
        '03_first_name' => null,
        '04_middle_name' => null,
        '05_suffix_name' => null,
        '06_address_number' => null,
        '07_address_street' => null,
        '08_address_post' => null,
        '09_address_apt' => null,
        '10_address_zip' => null,
        '11_mailing_address' => null,
        '12_mailing_apt' => null,
        '13_mailing_city' => null,
        '14_mailing_state' => null,
        '15_mailing_zip' => null,
        '16_party' => null,
        '17_dob' => null,
        '18_registration_date' => null,
        '19_ward_code' => null,
        '20_precinct_code' => null,
        '21_congressional_district' => null,
        '22_senate_district' => null,
        '23_house_district' => null,
        '24_voter_status' => null,
        '25_gender' => null,
    ];
    public $required = [
        '01_voter_id',
        '21_congressional_district',
        '22_senate_district',
        '23_house_district',
    ];

    public $percent_imported = 0;
    public $percent_verified = 0;
    public $percent_inserted = 0;

    public $failed_import_jobs = null;
    public $failed_verify_jobs = null;
    public $failed_insert_jobs = null;

    public $voters_filter = null;

    public $newly_created_count = null;
    public $newly_updated_count = null;
    public $newly_changed_count = null;

    // public function hydrate()
    // {
    // 	dd(1);
    // }

    public function mount($import_id)
    {
        $this->import_id = $import_id;

        // FIX FIRST LINE
        if ($import_id) {
            $import = Import::find($import_id);
            $file = new \SplFileObject(storage_path().'/app/'.$import->file, 'r+');
            $delimiter = $this->detectDelimiter($file);
            //dd($file, $delimiter);
            $firstrow = $file->fgetcsv($delimiter);
            $secondrow = $file->fgetcsv($delimiter);

            //dd($firstrow, $secondrow);

            if (count($firstrow) == count($secondrow) + 1) {
                //dd($firstrow);
                //dd();
                unset($firstrow[(count($firstrow) - 1)]);
                //dd($firstrow);
            }

            foreach ($firstrow as $index => $colname) {
                $firstrow[$index] = mb_convert_encoding($colname, 'UTF-8', 'UTF-8');
                //$firstrow[$index] = preg_replace('/[\x00-\x1F\x7F]/u', '', $colname);
                if ($firstrow[$index] != $colname) {
                    $array = str_split($colname);
                    $str = '';
                    //$ord = [];
                    foreach ($array as $ind => $val) {
                        if (ord($val) > 128) {
                            $str .= '-';
                        } else {
                            $str .= $val;
                        }
                    }
                    //dd($array, $str);
                    $firstrow[$index] = $str;
                }
            }
            $this->firstrow = $firstrow;
        }

        $this->available_states = Team::all()->pluck('data_folder_id')
                                             ->unique()
                                             ->reject(function ($state) {
                                                return !Schema::connection('voters')->hasTable('x_voters_'.$state.'_master');
                                             });
    }

    public function render()
    {
        $import = null;
        $voters = null;

        if ($this->import_id) {
            $import = Import::find($this->import_id);
        }
        // ============================> Figure out Step
        if ($import) {
            $this->step_1 = true;
            if ($import->column_map) {
                $this->step_2 = true;
                $this->field_map = $import->column_map;
            }
            if ($import->imported_at) {
                $this->step_3 = true;
            }
            if ($import->verified_at) {
                $this->step_4 = true;
            }
            if ($import->completed_at) {
                $this->step_5 = true;
            }
        }

        if ($this->municipality_lookup) {
            $this->municipalities = \App\Municipality::
                                    where('state', $this->state)
                                    ->where('name', 'like', $this->municipality_lookup.'%')
                                    ->orderBy('name')
                                    ->get();

            if ($this->municipalities->first()) {
                $this->municipality_id = $this->municipalities->first()->id;
            } else {
                $this->municipality_id = null;
            }
        } else {
            $this->municipalities = \App\Municipality::where('state', $this->state)
                                                     ->orderBy('name')
                                                     ->get();
        }

        $this->firstrecord = [];
        if ($this->step_1 && ! $this->step_2) {

            //$file = fopen(storage_path()."/app/".$import->file, 'r');

            if (! $import->file_count) {
                $file = new \SplFileObject(storage_path().'/app/'.$import->file, 'r');
                $file->seek(PHP_INT_MAX);
                $numrows = $file->key();
                $import->file_count = $numrows;
                $import->save();
            }

            //dd($numrows);
            $file = new \SplFileObject(storage_path().'/app/'.$import->file, 'r');

            $this->delimiter = $this->detectDelimiter($file);

            $delimiter = $this->delimiter;

            $dud = $file->fgetcsv($delimiter);
            $secondrow = $file->fgetcsv($delimiter);

            //dd($dud, $secondrow);

            // foreach ($this->firstrow as $key => $val) {
            // 	if (!$val) {
            // 		unset($this->firstrow[$key]);
            // 	}
            // }

            ksort($this->field_map);

            try {
                foreach ($this->firstrow as $index => $field) {
                    $this->firstrow[$index] = trim($field);
                    $this->firstrecord[trim($field)] = trim($secondrow[$index]);
                    // $this->firstrecord[$index] = trim($secondrow[$index]);
                }
            } catch (\Exception $e) {
                dd($import, $this->firstrow, $secondrow, $this->delimiter, $e->getMessage());
            }
        }
        //dd($this->firstrow, $this->firstrecord, $this->field_map);
        if ($this->step_2 && ! $this->step_3) {
            $this->firstfive = $this->getMatchedRecords($import->file, 5);
        }
        if ($this->step_2) {
            $failed = DB::table('failed_jobs')
                        ->where('payload', 'like', '%cf:import_municipality%')
                        ->where('failed_at', '>=', $import->created_at)
                        ->orderBy('failed_at', 'desc')
                        ->get()
                        ->toArray();

            if ($failed) {
                $this->failed_import_jobs = $failed;
            // $import->imported_at = null;
                // $import->started_at = null;
                // $import->save();
            } else {
            }
            $this->percent_imported = ceil(100 * ($import->imported_count / $import->file_count));
        }

        if ($this->step_3) {
            session(['import_table' => $import->table_name]);
            $voters = VoterImport::query();
            if ($this->voters_filter) {
                $voters = $voters->where('last_name', 'LIKE', $this->voters_filter.'%');
            }
            $voters = $voters->paginate(20);

            $failed = DB::table('failed_jobs')
                        ->where('payload', 'like', '%cf:verify_municipality%')
                        ->where('failed_at', '>=', $import->created_at)
                        ->orderBy('failed_at', 'desc')
                        ->get()
                        ->toArray();

            if ($failed) {
                $this->failed_verify_jobs = $failed;
                $import->verified_at = null;
                $import->save();
            } else {
                $this->percent_verified = ceil(100 * ($import->verified_count / $import->file_count));
            }
            //dd($this->voters);
        }
        if ($this->step_4) {
            $failed = DB::table('failed_jobs')
                        ->where('payload', 'like', '%cf:insert_municipality%')
                        ->where('failed_at', '>=', $import->created_at)
                        ->orderBy('failed_at', 'desc')
                        ->get()
                        ->toArray();

            if ($failed) {
                $this->failed_insert_jobs = $failed;
                $import->completed_at = null;
                $import->save();
            } else {
                $this->percent_inserted = ceil(100 * (($import->new_count + $import->updated_count) / $import->file_count));
            }
            //dd($this->voters);

            if ($import->reverting) {
                $this->percent_inserted = ceil(100 * (($import->new_count + $import->updated_count) / $import->file_count));
            }
        }

        if ($this->step_5) {
            $this->newly_created_count = VoterMaster::where('created_at', '>=', $import->started_at)
                                                    ->where('city_code', $import->municipality_id)
                                                    ->count();
            $this->newly_updated_count = VoterMaster::where('updated_at', '>=', $import->started_at)
                                                    ->where('created_at', '<', $import->started_at)
                                                    ->where('city_code', $import->municipality_id)
                                                    ->count();
        }

        return view('livewire.admin-upload-to-master.new-upload', compact('import', 'voters'));
    }

    public function detectDelimiter($file)
    {
        $delimiter = ',';
        $possibilities = ["\t", ';', '|', ','];
        $data_1 = [];
        $data_2 = [];

        foreach ($possibilities as $d) {
            //$dud = $file->fgetcsv($d);
            $data_1 = $file->fgetcsv($d);
            if (count($data_1) > count($data_2)) {
                $delimiter = count($data_1) > count($data_2) ? $d : $delimiter;
                $data_2 = $data_1;
            }
            $file->rewind();
        }
        $this->delimiter = $delimiter;

        return $delimiter;
    }

    public function fixCsv()
    {
        if ($this->import_id) {
            $import = Import::find($this->import_id);
        }
        $newfilepath = str_replace('.', '_fixed.', $import->file);
        $newfile = fopen(storage_path().'/app/'.$newfilepath, 'w');
        $oldfile = fopen(storage_path().'/app/'.$import->file, 'r');
        $first = true;

        while ($data = fgetcsv($oldfile, $this->delimiter)) {
            if ($first) {
                foreach ($data as $index => $colname) {
                    $data[$index] = preg_replace('/[\x00-\x1F\x7F]/u', '', $colname);
                }
                $first = false;
            }
            fputcsv($newfile, $data);
        }
        $import->file = $newfilepath;
        $import->save();
    }

    public function getMatchedRecords($filepath, $limit)
    {
        if (! $limit) {
            $limit = 10000;
        }

        $file = new \SplFileObject(storage_path().'/app/'.$filepath, 'r');
        $dud = $file->fgetcsv($this->delimiter);
        //dd($firstrow, $this->field_map);
        $lookup = [];
        foreach ($this->field_map as $index => $val) {
            if ($val) {
                $lookup[$val] = $index;
            }
        }
        //$lookup = array_flip($this->field_map);
        $indexmap = [];
        foreach ($this->firstrow as $index => $longfield) {
            if (isset($lookup[trim($longfield)])) {
                $indexmap[$index] = $lookup[trim($longfield)];
            }
        }
        //dd($indexmap);
        $rows = [];
        for ($i = 0; $i < $limit; $i++) {
            $rawrow = $file->fgetcsv($this->delimiter);
            $row = [];
            foreach ($rawrow as $index => $val) {
                if (isset($indexmap[$index])) {
                    $row[$indexmap[$index]] = $val;
                }
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function matchFields()
    {
        $import = Import::find($this->import_id);
        $import->column_map = $this->field_map;
        $import->save();
    }

    public function unmatch()
    {
        $import = Import::find($this->import_id);
        $import->column_map = null;
        $import->save();
        $this->step_2 = false;
        foreach ($this->field_map as $field => $val) {
            $this->field_map[$field] = null;
        }
    }

    public function startImport()
    {
        Artisan::queue('cf:import_municipality --import_id='.$this->import_id);
    }

    public function startVerify()
    {
        Artisan::queue('cf:verify_municipality --import_id='.$this->import_id);
    }

    public function startInsert()
    {
        Artisan::queue('cf:insert_municipality --import_id='.$this->import_id);
    }

    public function clearImport()
    {
        $import = Import::find($this->import_id);
        DB::connection('imports')->table($import->table_name)->truncate();
        $import->started_at = null;
        $import->imported_at = null;
        $import->imported_count = null;
        $import->save();
        $this->step_3 = false;
    }

    public function clearVerify()
    {
        $import = Import::find($this->import_id);
        $import->verified_at = null;
        $import->verified_count = null;
        $import->save();
        $this->step_4 = false;
    }

    public function revertInsert()
    {
        $this->step_5 = false;
        $import = Import::find($this->import_id);
        $import->reverting = true;
        $import->completed_at = null;
        $import->save();
        Artisan::queue('cf:revert_insert_municipality --import_id='.$this->import_id);
    }

    public function showChanges()
    {
    }

    public function guessFields()
    {
        // $lookup = [
        // 	'' => 'Record Sequence Number',
        // 	'01_voter_id' 			=> 'Voter ID Number',
        // 	'02_last_name' 			=> 'Last Name',
        // 	'03_first_name' 		=> 'First Name',
        // 	'04_middle_name' 		=> 'Middle Name',
        // 	'05_suffix_name' 		=> 'Title',
        // 	'06_address_number' 	=> 'Residential Address Street Number',
        // 	'07_address_street' 	=> 'Residential Address Street Name',
        // 	'08_address_post' 		=> 'Residential Address Street Suffix',
        // 	'09_address_apt' 		=> 'Residential Address Apartment Number',
        // 	'10_address_zip' 		=> 'Residential Address Zip Code',
        // 	'11_mailing_address' 	=> 'Mailing Address ｿ Street Number and Name',
        // 	'12_mailing_apt' 		=> 'Mailing Address - Apartment Number',
        // 	'13_mailing_city' 		=> 'Mailing Address - City or Town',
        // 	'14_mailing_state' 		=> 'Mailing Address - State',
        // 	'15_mailing_zip' 		=> 'Mailing Address - Zip Code',
        // 	'16_party' 				=> 'Party Affiliation',
        // 	'17_dob' 				=> 'Date of Birth',
        // 	'18_registration_date' 	=> 'Date of Registration',
        // 	'19_ward_code' 			=> 'Ward Number',
        // 	'20_precinct_code' 		=> 'Precinct Number',
        // 	'21_congressional_district' => 'Congressional District Number',
        // 	'22_senate_district' 	=> 'Senatorial District Number',
        // 	'23_house_district' 	=> 'State Representative District',
        // 	'24_voter_status' 		=> 'Voter Status',
        // ];

        // foreach ($this->field_map as $db_field => $file_field) {
        // 	if (!$file_field) {
        // 		//dd($db_field);
        // 		if (isset($lookup[$db_field])) {
        // 			//dd($file_field);
        // 			$this->field_map[$db_field] = $lookup[$db_field];
        // 		}
        // 	}
        // }

        // dd(	$this->step_1
        // 	, $this->step_2
        // 	, $this->step_3
        // 	, $this->step_4
        // 	, $this->step_5
        // 	, $this->step_6

        // 	, $this->import_id
        // 	, $this->municipality_id
        // 	, $this->municipality_lookup
        // 	, $this->municipalities

        // 	, $this->delimiter
        // 	, $this->first_has_fields

        // 	, $this->firstrow, $this->firstrecord, $this->firstfive

        // 	, $this->field_map

        // 	, $this->percent_imported
        // 	, $this->percent_verified
        // 	, $this->percent_inserted

        // 	, $this->failed_import_jobs
        // 	, $this->failed_verify_jobs
        // 	, $this->failed_insert_jobs

        // 	, $this->voters_filter

        // 	, $this->newly_created_count
        // 	, $this->newly_updated_count
        // 	, $this->newly_changed_count
        // 	);

        $lookup = [
            // '' => 'Record Sequence Number',
            '01_voter_id' 			=> ['Voter ID Number',
                                        'id',
                                        'voterid',
                                        'voter id', ],
            '02_last_name' 			=> ['Last Name', 'last'],
            '03_first_name' 		=> ['First Name', 'first'],
            '04_middle_name' 		=> ['Middle Name', 'middle'],
            '05_suffix_name' 		=> ['Title', 'name title'],
            '06_address_number' 	=> ['Residential Address Street Number',
                                        'streetno',
                                        'address number', ],
            '07_address_street' 	=> ['Residential Address Street Name',
                                        'address street', ],
            '08_address_post' 		=> ['Residential Address Street Suffix',
                                        'address suffix',
                                        'suffix',
                                        'address fraction', ],
            '09_address_apt' 		=> ['Residential Address Apartment Number',
                                        'apt',
                                        'aptno',
                                        'address apt', ],
            '10_address_zip' 		=> ['Residential Address Zip Code',
                                        'zip',
                                        'address zip', ],
            '11_mailing_address' 	=> ['Mailing Address ｿ Street Number and Name',
                                        'mailing street', ],
            '12_mailing_apt' 		=> ['Mailing Address - Apartment Number',
                                        'mailing apt no',
                                        'mailing apt', ],
            '13_mailing_city' 		=> ['Mailing Address - City or Town',
                                        'mailing city', ],
            '14_mailing_state' 		=> ['Mailing Address - State',
                                        'mailing state', ],
            '15_mailing_zip' 		=> ['Mailing Address - Zip Code',
                                        'mailing zip', ],
            '16_party' 				=> ['Party Affiliation', 'party'],
            '17_dob' 				=> ['Date of Birth', 'dob'],
            '18_registration_date' 	=> ['Date of Registration',
                                        'registered',
                                        'registration date', ],
            '19_ward_code' 			=> ['Ward Number', 'ward'],
            '20_precinct_code' 		=> ['Precinct Number', 'precinct'],
            '21_congressional_district' => ['Congressional District Number', 'congress district'],
            '22_senate_district' 	=> ['Senatorial District Number', 'senate district'],
            '23_house_district' 	=> ['State Representative District', 'house district'],
            '24_voter_status' 		=> ['Voter Status'],
        ];

        // Create permutations - spaces / underscores
        foreach ($lookup as $needed_field => $aliases) {
            foreach ($aliases as $alias) {
                $lookup[$needed_field][] = str_replace(' ', '_', $alias);
            }
        }

        // Create permutations - case
        foreach ($lookup as $needed_field => $aliases) {
            foreach ($aliases as $alias) {
                $lookup[$needed_field][] = strtolower($alias);
            }
        }

        foreach ($this->firstrow as $file_field) {
            foreach ($lookup as $needed_field => $aliases) {
                if (in_array($file_field, $aliases)) {
                    $this->field_map[$needed_field] = $file_field;
                }
            }
        }
    }
}
