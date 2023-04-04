<?php

namespace App\Console\Commands\Campaign;

use Illuminate\Console\Command;
use App\CampaignList;
use Auth;
use Carbon\Carbon;

class CacheLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:cache_lists {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates and updates the cached_voters for all lists.';

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

        $lists = CampaignList::where('name', '!=', 'County Super')
                             ->where('name', 'NOT LIKE', '01-PB%')
                             //->where('updated_at', '<', Carbon::now()->subHour())
                             ->latest()
                             ->get();

        if ($this->option('id')) {
            $lists = CampaignList::where('id', '=', $this->option('id'))
                             ->get();
        }
                             
        echo "Starting on ".$lists->count()." lists.\n";
        foreach ($lists as $list) {
            try {
                if ($list->team) {
                    Auth::logIn($list->user);
                    $slice_name = $list->team->db_slice;
                    session()->put('team_table', $slice_name);
                    session()->put('team_state', 'MA');
                    echo "\t".$list->team->name." - ".$slice_name." - ".$list->name."\n";

                    echo "\t\t".$list->voter_count." Voters\n";
                    $list->cacheVoters();
                } else {
                    echo "\tMISSING TEAM: ".$list->name."\n";
                }
            } catch (\Exception $e) {
                echo "\t***** ERROR: ".substr($e->getMessage(), 0, 75)."\n";
            }
        }
    }
}
