<?php

namespace App\Console\Commands\Campaign;

use Illuminate\Console\Command;
use App\Municipality;
use App\VoterMaster;
use Illuminate\Support\Str;
use App\Team;
use App\UserUpload;
use App\UserUploadData;

class NameCityList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:name_city_list {--city=} {--team=} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports a file that just has name and city';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected $upload;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = storage_path('/app/csvs/canton-dogs.csv');
        if ($this->option('file')) {
            $file = storage_path($this->option('file'));
        }

        //dd(VoterMaster::count());

        $upload = UserUpload::where('name', basename($file))->first();
        if (!$upload) {

            $team_id = null;
            if (!$this->option('team')) {

                $teams = Team::where('app_type', 'campaign')
                                    ->orderBy('name')
                                    ->get()
                                    ->keyBy('id');
                                    //dd($cities);
                $team_arr = [];
                foreach ($teams as $team) {
                    $team_arr[$team->id] = $team->name;
                }

                //dd($team_arr);

                $val = $this->choice(
                        'Which TEAM is this for?',
                        $team_arr
                    );
                $team_id = array_search($val, $team_arr);
            } else {
                $team_id = $this->option('team');
            }
            
            $upload = new UserUpload;
            $upload->team_id = $team_id;
            $upload->user_id = 257; //Laz
            $upload->name = basename($file);
            $upload->save();
            
        }
        $this->upload = $upload;

        $col_first = null;
        $col_last  = null;
        $city_code = null;

        if ($this->option('city')) {
            $city = Municipality::where('name', 'LIKE', $this->option('city'))->first();
            if ($city) {
                $city_code = $city->code;
            }
        }

        $cities = Municipality::where('state', 'MA')
                                ->orderBy('name')
                                ->get()
                                ->keyBy('code');
                                //dd($cities);
        $city_arr = [];
        foreach ($cities as $city) {
            $city_arr[$city->code] = $city->name;
        }
        //dd($city_arr);

        $matches = 0;
        $csv = fopen($file, 'r');
        $row = 0;
        while (($data = fgetcsv($csv, 1000, ",")) !== FALSE) {
            $row++;
            if ($row == 1) {
                
                //dd($indexed);
                // choose first name col
                $val = $this->choice(
                    'Which contains FIRST NAME?',
                    $data
                );
                $col_first = array_search($val, $data);

                // choose last name col
                $val = $this->choice(
                    'Which contains LAST NAME?',
                    $data
                );
                $col_last = array_search($val, $data);
                // choose city col
                // select from municipality list
                if (!$city_code) {
                    $city_code = $this->choice(
                        'Which CITY is this?',
                        $city_arr
                    );
                }
                continue;
            }
            //dd($col_first, $col_last, $data);
            $first_name = trim($data[$col_first]);
            $last_name  = trim($data[$col_last]);
            if ($col_first == $col_last) {
                $name_arr = explode(' ', trim($data[$col_first]));
                $first_name = $name_arr[0];
                $last_name = $name_arr[count($name_arr)-1];
            }

            print_r($data);

            $query = VoterMaster::where('first_name', 'LIKE', $first_name)
                                 ->where('last_name', 'LIKE', $last_name)
                                 ->where('city_code', $city_code);
                                 
            //$this->line(Str::replaceArray('?', $query->getBindings(), $query->toSql()));

            $voters = $query->get();

            if ($voters->count() == 1) {
                $voter = $voters->first();
                $this->info("√ Direct match: ".$voter->id." - ".$voter->name." - ".$voter->full_address);
                $matches++;

                $this->addVoterToUpload($voter->id, $row, $data);
                continue;
            }

            /*
            if ($voters->count() < 1) {
                $voters = VoterMaster::where('first_name', 'LIKE', $first_name)
                                 ->where('last_name', 'LIKE', $last_name)
                                 ->get();
            }
            */

            if ($voters->count() < 1) {
                $voters = VoterMaster::where('first_name', 'LIKE', substr($first_name, 0, 1).'%')
                                 ->where('last_name', 'LIKE', $last_name)
                                 ->where('city_code', $city_code)
                                 ->get();
            }

            if ($voters->count() < 1) {
                $voters = VoterMaster::where('first_name', 'LIKE', substr($first_name, 0, 2).'%')
                                 ->where('last_name', 'LIKE', substr($last_name, 0, 2).'%')
                                 ->where('city_code', $city_code)
                                 ->get();
            }

            if ($voters->count() < 1) {
                $voters = VoterMaster::where('first_name', 'LIKE', substr($first_name, 0, 1).'%')
                                 ->where('last_name', 'LIKE', substr($last_name, 0, 1).'%')
                                 ->where('city_code', $city_code)
                                 ->get();
            }

            /*
            if ($voters->count() < 1) {
                $voters = VoterMaster::where('first_name', 'LIKE', substr($first_name, 0, 1).'%')
                                 ->where('last_name', 'LIKE', substr($last_name, 0, 1).'%')
                                 ->get();
            }
            */

            $matched_address = false;
            foreach ($voters as $voter) {
                foreach ($data as $val) {
                    if ($voter->full_address) {
                        if (strtoupper(substr(trim($voter->full_address), 0, 8)) 
                            == strtoupper(substr(trim($val), 0, 8))) {
                            $this->info("√ Address match: ".$voter->id." - ".$voter->name." - ".$voter->full_address);
                            
                            $this->addVoterToUpload($voter->id, $row, $data);
                            $matched_address = true;
                            break;
                        }
                    }
                }
            }
            if ($matched_address) {
                $matches++;
                continue;
            }

            $this->line($voters->count()." possible matches found:");
            

            $voter_arr = [];
            $voter_arr[] = "SKIP";
            $voters = $voters->sortBy('first_name')
                             ->sortBy('last_name');
            foreach ($voters as $voter) {
                $voter_arr[] = $voter->id." - ".$voter->name." - ".$voter->full_address;
            }

            $upload_data = UserUploadData::where('upload_id', $this->upload->id)
                                     ->where('line', $row)
                                     ->first();

            if ($upload_data) {
                if ($upload_data->voter_id) {
                    continue;
                }
            }

            $voter_code = 'SKIP';
            if ($voters->count() > 0) {
                    $voter_code = $this->choice(
                    'Which VOTER is this?',
                    $voter_arr
                );
            }

            

            $voter_id = null;
            if ($voter_code != 'SKIP') {
                $voter_id = explode(' ', trim($voter_code))[0];
            }

            if ($voter_id) {
                $matches++;
            }

            $this->addVoterToUpload($voter_id, $row, $data);

        }
        $this->line("Matches found: $matches");
        return Command::SUCCESS;
    }

    public function addVoterToUpload($voter_id, $line, $data)
    {
        $upload_id = $this->upload->id;
        $upload_data = UserUploadData::where('upload_id', $upload_id)
                                     ->where('line', $line)
                                     ->first();
        if (!$upload_data) {
            $upload_data = new UserUploadData;
            $upload_data->team_id = $this->upload->team_id;
            $upload_data->upload_id = $upload_id;
            $upload_data->line = $line;
            $upload_data->data = $data;
            $upload_data->save();
        }
        $upload_data->voter_id = $voter_id;
        $upload_data->save();
    }


}
