<?php

namespace App\Console\Commands;

use App\Models\Admin\DataImport;
use App\Models\Admin\DataJob;
use App\Team;
use App\VoterMaster;
use App\VoterSlice;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

use App\Traits\Admin\FileProcessingTrait;

use Carbon\Carbon;

use App\District;



class PopulateSlices extends Command
{
    use FileProcessingTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:populate_slices      {--all} 
                                                    {--after=} 
                                                    {--overwrite} 
                                                    {--skip_dump} 
                                                    {--slice=} 
                                                    {--ignore_state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes through each slice and populates the voter tables from it.';

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

        ///////////////////////////////////////////////////////////////////

        $missing_master = VoterSlice::whereNull('master');

        if ($missing_master->first()) {

            echo "\n";

            $this->error(' *** '.$missing_master->count().' slices have missing master tables. ***');

            $options = ['proceed' => 'Proceed anyway', 
                        'update' => 'Set all null masters to x_voters_MA_master (if name contains "_MA_")', 
                        'quit' => 'Quit'];

            $action = $this->choice('Do you want to:', $options);

            if ($action == 'quit') dd('Exiting.');

            if ($action == 'update') {

                foreach (VoterSlice::whereNull('master')
                                   ->where('name', 'like', '%_MA_%')
                                   ->get() as $slice_update) {

                    $slice_update->master = 'x_voters_MA_master';
                    $slice_update->save();

                    echo $slice_update->name." \t\t set to x_voters_MA_master\n";
                }

            }
        }

        ///////////////////////////////////////////////////////////////////

        $chosen_slice = $this->option('slice');

        if (!$chosen_slice && !$this->option('all') && !$this->option('after')) {

            $options = VoterSlice::all()->pluck('name')->sort()->toArray();
            $options = $this->rekeyStartingAtOne($options);

            foreach($options as $key => $option) {
                $name = null;

                $parts = explode('_', $option);
                $state = (isset($parts[1])) ? $parts[1] : null;
                $type  = (isset($parts[2])) ? $parts[2] : null;
                $code   = (isset($parts[3])) ? $parts[3] : null;

                $look = District::where('state', $state)
                                ->where('type', $type)
                                ->where('code', $code)
                                ->first();

                $name = ($look) ? $look->name : null;

                $options[$key] = $option."\t\t".$name;
            }

            $chosen_slice = $this->choice('Pick a slice, any slice:', $options);

            $chosen_slice = trim(substr($chosen_slice, 0, strpos($chosen_slice, "\t")));
            
            $confirm = $this->confirm('CONFIRM: '.$chosen_slice, true);

            if (!$confirm) dd('Exiting.');
        }

        ///////////////////////////////////////////////////////////////////

        if ($this->option('after')) {
            $slices = VoterSlice::where('id', '>', $this->option('after'))->get();
        } else {
            $slices = VoterSlice::all();
        }
        

        foreach ($slices as $slice) {
            
            $this->start_time = Carbon::now();
            $this->expected_num_rows = null;

            if ($chosen_slice) {
                if ($chosen_slice != $slice->name) {
                    continue;
                }
            }

            if ($this->option('ignore_state')) {
                // if ('x_MA_STATE' == $slice->name) {
                if (substr($slice->name, 0, 2) == 'x_' && substr($slice->name, -6) == '_STATE') {
                    continue;
                }
            }

            echo "\n";
            $this->info($slice->name);

            if (Schema::hasTable($slice->name)
                && DB::table($slice->name)->count() > 0
                && !$this->option('overwrite')
                ) {
                $this->info($slice->name." slice already populated. Skipping.");
                continue;
            }

            if (!$slice->master) {
                $this->info($slice->name." does not have a Master Voter File designated. Skipping.");
                continue;
            }

            // echo "\n ".date('Y-m-d h:i:s', time()).' ============> Starting to populate '.$slice->name."\n";

            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $password = ($password) ? '-p'.$password : null; // Otherwise keeps prompting for password
            $voter_database = env('DB_VOTER_DATABASE');
            $path_to_mysql = env('PATH_TO_MYSQL'); // "/usr/local/Cellar/mysql@5.7/5.7.24/bin/";

            $path = storage_path().'/mnt/sqldumps/temp';
            if (! file_exists($path)) {
                echo "Making directory $path";
                mkdir($path, 0755, true);
            }

            //////////////////////////////////////////////////////////////////////////////
            //
            //  VOTERS
            //
            //////////////////////////////////////////////////////////////////////////////


            // ==================================================> 1. create mysqldump file using --where
            if (! $this->option('skip_dump')) {

                // $sql_dump_command = $path_to_mysql."mysqldump -u $username ".$password." $voter_database x_voters_MA_master --where=\"".$slice->sql."\" > $path/".$slice->name.'_TEMP.sql';
                $sql_dump_command = $path_to_mysql."mysqldump -u $username ".$password." $voter_database ".$slice->master." --where=\"".$slice->sql."\" > $path/".$slice->name.'_TEMP.sql';

                // echo "Trying: ".$sql_dump_command."\n";
                // shell_exec($sql_dump_command);
                exec($sql_dump_command, $output, $return_var);
                if ($return_var != 0) {
                    $this->info($slice->name." Error in  MySQL Dump. Skipping.");
                    continue;
                }
            }

            // ==================================================> 2. expand to temp table
            $main_database = env('DB_DATABASE');
            $sql_load_command = $path_to_mysql."mysql -u $username ".$password." $main_database < $path/".$slice->name.'_TEMP.sql';
            echo $sql_load_command."\n";
            shell_exec($sql_load_command);

            if (Schema::hasTable($slice->name)) {

                // ==================================================> 3. rename current slice to archive
                $archive_database = env('DB_ARCHIVE_DATABASE');
                $rename_to_archive = "RENAME TABLE $main_database.".$slice->name.' TO '.$archive_database.'.'.date('Y_m_d_h_i_s_', time()).$slice->name;
                echo $rename_to_archive."\n";
                DB::statement($rename_to_archive);
            }

            // ==================================================> 4. rename temp table to current slice

            // $rename_to_current = "RENAME TABLE $main_database.x_voters_MA_master TO ".$slice->name;
            $rename_to_current = "RENAME TABLE $main_database.".$slice->master." TO ".$slice->name;
            echo $rename_to_current."\n";
            DB::statement($rename_to_current);

            //////////////////////////////////////////////////////////////////////////////
            //
            //  COUNTS
            //
            //////////////////////////////////////////////////////////////////////////////

            $voters_count = $slice->voters_count = DB::table($slice->name)->count();
            $slice->save();

            foreach ($slice->teams as $team) {
                $team->refreshCount();
                $team->save();
            }
            $slice->voters_count = $voters_count;
            $slice->save();

            $c1 = "\e[0;37;44m ";
            $c2 = "\e[0m\n ";
            $this->info($c1.$slice->name.' populated. Voters: '.number_format($voters_count).$c2);

            echo "\n".$this->progress($row = null)."\n\n";
            //$this->drawPizza();
        }
    }

    public function drawPizza()
    {
        echo "

⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣠⣤⣶⣶⣦⣄⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢰⣿⣿⣿⣿⣿⣿⣿⣷⣦⡀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢠⣷⣤⠀⠈⠙⢿⣿⣿⣿⣿⣿⣦⡀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣠⣿⣿⣿⠆⠰⠶⠀⠘⢿⣿⣿⣿⣿⣿⣆⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣼⣿⣿⣿⠏⠀⢀⣠⣤⣤⣀⠙⣿⣿⣿⣿⣿⣷⡀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⢠⠋⢈⣉⠉⣡⣤⢰⣿⣿⣿⣿⣿⣷⡈⢿⣿⣿⣿⣿⣷⡀
⠀⠀⠀⠀⠀⠀⠀⡴⢡⣾⣿⣿⣷⠋⠁⣿⣿⣿⣿⣿⣿⣿⠃⠀⡻⣿⣿⣿⣿⡇
⠀⠀⠀⠀⠀⢀⠜⠁⠸⣿⣿⣿⠟⠀⠀⠘⠿⣿⣿⣿⡿⠋⠰⠖⠱⣽⠟⠋⠉⡇
⠀⠀⠀⠀⡰⠉⠖⣀⠀⠀⢁⣀⠀⣴⣶⣦⠀⢴⡆⠀⠀⢀⣀⣀⣉⡽⠷⠶⠋⠀
⠀⠀⠀⡰⢡⣾⣿⣿⣿⡄⠛⠋⠘⣿⣿⡿⠀⠀⣐⣲⣤⣯⠞⠉⠁⠀⠀⠀⠀⠀
⠀⢀⠔⠁⣿⣿⣿⣿⣿⡟⠀⠀⠀⢀⣄⣀⡞⠉⠉⠉⠉⠁⠀⠀⠀⠀⠀⠀⠀⠀
⠀⡜⠀⠀⠻⣿⣿⠿⣻⣥⣀⡀⢠⡟⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⢰⠁⠀⡤⠖⠺⢶⡾⠃⠀⠈⠙⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠈⠓⠾⠇⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
        ";
    }
}
