<?php

namespace Database\Seeders;

use App\Account;
use App\County;
use App\Municipality;
use App\Team;
use App\VoterMaster;
use App\VoterSlice;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoterSlicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    // NAMING SYSTEM
    //
    // x_H_0001				House district
    // x_S_0001				Senate district
    // x_M_boston			Town/City/Municipality
    // x_M_worcester_W1 	Ward 1 of Worcester
    // x_C_hampshire 		County
    // x_STATE              State
    // x_SAMPLE             Sample District
    // x_O_lowellchelms		Other / Custom
    // x_F_7 				Federal / Congress
    //

    public function run()
    {
        $this->command->info('Creating Voter Slices...');

        $state = 'MA';

        // Sample Table, for non-active accounts or other testing, etc.
        // $samplewhere = "(first_name = 'Jane' OR first_name = 'John')";
        $samplewhere = 'address_number < 10';
        $sampletable = 'x_'.$state.'_SAMPLE';
        $sampleslice = $this->createTable($samplewhere, $sampletable);
        $this->createHouseholds($sampletable);

        $accountids = Account::whereNotNull('billygoat_id')->pluck('id');
        $teams = Team::where('district_type', 'A')
                     ->orWhereIn('account_id', $accountids)
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
                $where = 'city_code = '.$team->district_id;
                $municipality_name = '';
                $municipality = Municipality::find($team->district_id);
                if ($municipality) {
                    $municipality_name = $municipality->name;
                }
                $table_name = 'x_'.$state.'_M_'.$municipality_name;
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

        //      $i = 1;
   //      foreach($cities as $district) {
   //      	if ($i > $limit) break;
   //      	$where 		= 'address_city="'.$district.'"';
   //      	$table_name = 'x_M_'.str_replace(' ', '', strtolower($district)); //Hyphen cause issues
   //      	if (VoterMaster::whereRaw($where)->count() > 0) {
            // 	$this->createTable($where, $table_name);
            // 	$this->createHouseholds($table_name);
            // 	$i++;
            // }
   //      }

   //      $accounts = Account::all();
   //      foreach($accounts as $theaccount) {
   //      	foreach($theaccount->teams as $theteam) {
   //      		$slice = VoterSlice::inRandomOrder()->take(1)->first();
   //      		$theteam->db_slice = $slice->name;
   //      		$theteam->save();
   //      	}
   //      }
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
