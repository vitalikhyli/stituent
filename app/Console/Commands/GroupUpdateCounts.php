<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Group;

class GroupUpdateCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:group_update_counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes sure all groups have the right static count.';

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
        $groups = Group::take(100)
                       ->withCount('groupPerson as gpcount')
                       ->havingRaw('gpcount <> people_count')
                       ->get();

        foreach ($groups as $group) {
            $group->updatePeopleCounts();
        }
        return Command::SUCCESS;
    }
}
