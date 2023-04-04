<?php

namespace App\Console\Commands\Seeding;

use App\Account;
use App\County;
use App\Municipality;
use App\Team;
use App\VoterMaster;
use App\VoterSlice;
use DB;
use Illuminate\Console\Command;

class Slices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:seed_slices';

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

    // NAMING SYSTEM
    //
    // x_H_0001             House district
    // x_S_0001             Senate district
    // x_M_boston           Town/City/Municipality
    // x_M_worcester_W1     Ward 1 of Worcester
    // x_C_hampshire        County
    // x_STATE              State
    // x_SAMPLE             Sample District
    // x_O_lowellchelms     Other / Custom
    // x_U_CityNamesEtc     Universities
    // x_F_7                Federal / Congress
    //

    public function handle()
    {

    //if (env('LOCAL_MACHINE') != 'Slothe')             return;
        if (! $this->confirm('SLICES: Do you wish to continue?')) {
            return;
        }

        echo date('Y-m-d h:i:s')." Starting Voter Slices\n";

        $state = 'MA';

        // Sample Table, for non-active accounts or other testing, etc.
        // $samplewhere = "(first_name = 'Jane' OR first_name = 'John')";
        $samplewhere = 'address_number < 6';
        $sampletable = 'x_'.$state.'_SAMPLE';
        $sampleslice = $this->createTable($samplewhere, $sampletable);
        $this->createHouseholds($sampletable);

        $accountids = Account::whereNotNull('billygoat_id')->pluck('id');
        $teams = Team::where('district_type', 'A')
                     ->orWhereIn('account_id', $accountids)
                     ->orWhere('old_cc_id', 199) // Welch
                     ->orWhere('old_cc_id', 441) // Malia
                     ->orderBy('old_cc_id')
                     ->get();

        foreach ($teams as $team) {
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
            $team->db_slice = $slice->name;
            $team->save();
        }
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
