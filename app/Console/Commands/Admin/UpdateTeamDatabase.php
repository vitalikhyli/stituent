<?php

namespace App\Console\Commands\Admin;

use App\Team;
use App\TenantModels\DataUpdate;
use Artisan;
use Carbon\Carbon;
use DB;
use Illuminate\Console\Command;

class UpdateTeamDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'st:update_team_database {--team_id=} {--clear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates or updates a team database. Migrates and populates. If doesn\'t exists, creates.';

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
        $team_id = $this->option('team_id');
        if (! $team_id) {
            dd('Gotta include a team ID, i.e. st:update_team_database --team_id=2');
        }
        $team = Team::find($team_id);
        if (! $team) {
            dd('No team found for team id '.$team_id);
        }

        // Creates a new database for the team, runs migrations, copies data
        $query = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME =  ?';
        $db = DB::select($query, [$team->database]);
        if (empty($db)) {
            DB::statement('CREATE DATABASE '.$team->database);
        } else {
            // DB already exists!!
            if ($this->option('clear')) {
                DB::statement('DROP DATABASE '.$team->database);
                DB::statement('CREATE DATABASE '.$team->database);
            }
        }

        config(['database.connections.tenant.database' => $team->database]);
        config(['database.default' => 'tenant']);
        DB::purge('tenant');
        DB::setDefaultConnection('tenant');

        //dd(config());

        //dd(\DB::connection());

        $errorcode = Artisan::call('migrate');

        // Copy over User and Team data to new DB

        // ==========================================> TESTING
        $du = new DataUpdate;
        $du->team_id = $team->id;
        $du->type = 'H'; // $team->type
        $du->district_id = 34; // $team->district_id

        $du->voter_count = 17000;
        $du->voter_current = 0;
        $du->elections_count = 27000;
        $du->elections_current = 0;
        $du->district_count = 600;
        $du->district_current = 0;

        $du->total_count = $du->voter_count + $du->elections_count + $du->district_count;
        $du->total_current = 0;

        $du->log = '';

        $du->save();

        for ($i = 0; $i <= $du->voter_count; $i++) {
            $du->voter_current = $i;
            $du->total_current += 1;
            $du->save();
        }
        for ($i = 0; $i <= $du->elections_count; $i++) {
            $du->elections_current = $i;
            $du->total_current += 1;
            $du->save();
        }
        for ($i = 0; $i <= $du->district_count; $i++) {
            $du->district_current = $i;
            $du->total_current += 1;
            $du->save();
        }
        $du->completed_at = Carbon::now();
        $du->save();

        if (! $team->activated_at) {
            // Send Notification with link to dashboard
            $team->activated_at = Carbon::now();
            $team->save();
        }
    }
}
