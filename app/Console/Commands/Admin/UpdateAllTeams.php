<?php

namespace App\Console\Commands\Admin;

use Illuminate\Console\Command;

use App\Team;


class UpdateAllTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:update_all_teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates all teams with touch(), triggers Teamwork model boot updating()';

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
     * @return int
     */
    public function handle()
    {
        foreach(Team::all() as $team) {
            $team->touch();
            $this->info($team->uuid."\t".$team->slug);
        }
    }
}
