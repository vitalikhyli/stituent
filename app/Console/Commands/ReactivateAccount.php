<?php

namespace App\Console\Commands;

use App\County;
use App\Models\CC\CCUser;
use App\Municipality;
use App\Team;
use App\VoterSlice;
use DB;
use Illuminate\Console\Command;

class ReactivateAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:reactivate {--login=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactivates all users for a single account. Creates and populates a slice if doesnt exist.';

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
        if (! $this->option('login')) {
            echo 'Please add a username, i.e. php artisan cf:reactivate --login=lmorrison';
            exit;
        }
        $login = $this->option('login');
        $cc_user = CCUser::where('login', $login)->first();

        if (! $cc_user) {
            echo 'No user found for '.$this->option('login');
            exit;
        }
        $campaign_id = $cc_user->campaignID;

        $team = Team::where('app_type', 'office')
                     ->where('old_cc_id', $campaign_id)
                     ->first();
        if (! $team) {
            echo 'No team found for old campaign id '.$campaign_id;
            exit;
        }

        $slice = $this->createSliceIfNeeded($team);

        if (! $slice) {
            echo 'Slice Problem';
            exit;
        }
        $team->db_slice = $slice->name;
        $team->save();

        $slice_options = ['--slice' => $slice->name, '--overwrite' => true];
        $this->call('cf:populate_slices', $slice_options);

        $options = [];
        $options['--campaign'] = $campaign_id;

        $this->call('st:seed_people', $options);
        $this->call('st:seed_groups', $options);
        $this->call('st:seed_cases', $options);
        $this->call('st:seed_contacts', $options);
        $this->call('st:seed_files', $options);
        $this->call('st:seed_bulk', $options);

        $team->refreshCount();
    }

    public function createSliceIfNeeded($team)
    {
        $state = 'MA';
        $where = '';
        $table_name = '';

        if ($team->district_type == 'A') {
            $where = '1';
            // $where      = null;
            $table_name = 'x_'.$state.'_STATE';
        }
        if ($team->district_type == 'H') {
            $where = 'house_district = '.$team->district_id;
            $table_name = 'x_'.$state.'_H_'.str_pad($team->district_id, '0', 4, STR_PAD_LEFT);
        }
        if ($team->district_type == 'S') {
            $where = 'senate_district = '.$team->district_id;
            $table_name = 'x_'.$state.'_S_'.str_pad($team->district_id, '0', 4, STR_PAD_LEFT);
        }
        if ($team->district_type == 'T') {
            if ($team->old_cc_id == 409) {
                // NU
                $where = '(city_code = 35 OR city_code = 48 OR city_code = 196 OR city_code = 73)';
                $table_name = 'x_'.$state.'_U_BostonNahantBurlingtonDedham';
            } else {
                $where = 'city_code = '.$team->district_id;
                $municipality_name = '';
                $municipality = Municipality::find($team->district_id);
                if ($municipality) {
                    $municipality_name = $municipality->name;
                }
                $table_name = 'x_'.$state.'_M_'.$municipality_name;
            }
        }
        if ($team->district_type == 'O') {
            $where = 'county_code = '.$team->district_id;
            $county_name = '';
            $county = County::find($team->district_id);
            if ($county) {
                $county_name = $county->name;
            }
            $table_name = 'x_'.$state.'_C_'.$county_name;
        }
        if ($team->district_type == 'U') {
            if ($team->old_cc_id == 357) {
                // Golden: Lowell & Chelmsford
                $where = '(city_code = 56 OR city_code = 160)';
                $table_name = 'x_'.$state.'_O_lowellchelmsford';
            }
        }
        if ($team->district_type == 'W') {
            $table_name = 'x_'.$state.'_STATE';
        }

        if (! $table_name) {
            $table_name = 'x_'.$state.'_SAMPLE';
        }

        $slice = VoterSlice::where('name', $table_name)->first();

        if (! $slice) {
            $slice = $this->createTable($where, $table_name);
            $this->createHouseholds($table_name);
        }

        return $slice;
    }

    public function createTable($where, $table_name)
    {
        $db_land = env('DB_VOTER_DATABASE');

        DB::statement('CREATE TABLE IF NOT EXISTS `'.$table_name.'` LIKE x__template_voters');

        $slice = new VoterSlice;
        $slice->sql = $where;
        $slice->name = $table_name;
        $slice->save();

        echo 'Created: '.$table_name."\n";

        return $slice;
    }

    public function createHouseholds($table_name)
    {
        $hh_table = $table_name.'_hh';

        DB::statement('CREATE TABLE IF NOT EXISTS `'.$hh_table.'` LIKE x__template_households');

        echo 'Created: '.$hh_table."\n";
    }
}
