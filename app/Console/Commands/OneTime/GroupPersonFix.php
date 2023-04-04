<?php

namespace App\Console\Commands\OneTime;

use Illuminate\Console\Command;
use App\GroupPerson;

class GroupPersonFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:group_person_fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Group Person was getting wrong team_id, this corrects it';

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
        $needsfix = GroupPerson::where('created_at', '>', '2020-10-01')
                               ->with('group', 'person')
                               ->get();

        $count = 0;

        foreach ($needsfix as $gp) {
            if (!$gp->group) {
                continue;
            }
            if (!$gp->person) {
                continue;
            }
            if ($gp->team_id != $gp->group->team_id) {
                echo ($count++).". ".$gp->person->name."\n"; 
                $gp->team_id = $gp->group->team_id;
                $gp->save();
            }
        }
    }
}
