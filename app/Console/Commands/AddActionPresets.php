<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Action;

class AddActionPresets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cf:action_presets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds the default Actions for the campaign side';

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
        $presets = [

        ];

        foreach ($presets as $preset) {
            $action = Action::where('preset')->where('name', $preset)->first();
            if (!$action) {
                $action = new Action;
                $action->preset = true;
                $action->name = $preset;
                $action->user_id = 1;
                $action->team_id = 1;
                $action->save();
                echo "Added preset $preset\n";
            }
        }
    }
}
